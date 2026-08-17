<?php

namespace App\Livewire\Registration;

use App\Models\Edition;
use App\Models\Inscription;
use App\Models\Participant;
use App\Models\Profil;
use App\Models\Region;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Component;

/**
 * Formulaire d'inscription public, découpé en trois étapes.
 *
 * La validation est faite étape par étape côté serveur, puis rejouée
 * intégralement à la soumission finale : le découpage est un confort
 * d'affichage, il ne doit jamais servir de garantie de sécurité.
 */
class RegisterForm extends Component
{
    public Edition $edition;

    /** Étape courante : 1 identité, 2 participation, 3 confirmation. */
    public int $etape = 1;

    public const DERNIERE_ETAPE = 3;

    // --- Étape 1 : identité ---
    public string $nom = '';
    public string $prenom = '';
    public string $email = '';
    public string $telephone = '';
    public string $structure = '';
    public string $fonction = '';
    public string $secteur_activite = '';

    // --- Étape 2 : participation ---
    public ?int $region_id = null;
    public ?int $profil_id = null;
    public array $jours = [];
    public string $besoins_particuliers = '';
    public string $source_connaissance = '';

    // --- Étape 3 : confirmation ---
    public bool $accepte_conditions = false;

    /**
     * Champ leurre : invisible à l'écran, rempli uniquement par les robots.
     * Une soumission avec ce champ renseigné est rejetée silencieusement.
     */
    public string $site_web = '';

    public bool $inscriptionTerminee = false;
    public ?string $codeGenere = null;
    public ?int $montantRegle = null;

    public function mount(Edition $edition): void
    {
        abort_unless($edition->estOuverte(), 403, 'Les inscriptions pour cette édition sont fermées.');

        $this->edition = $edition;

        // Par défaut, le participant est annoncé sur toute la durée du forum.
        $this->jours = collect($this->joursForum())->pluck('valeur')->all();
    }

    /**
     * Règles complètes du formulaire. Les règles d'une étape donnée sont
     * extraites de cet ensemble, pour qu'il n'existe qu'une seule source.
     *
     * @return array<string, mixed>
     */
    protected function rules(): array
    {
        $joursValides = collect($this->joursForum())->pluck('valeur')->implode(',');
        $secteurs = implode(',', array_keys(config('folem.secteurs', [])));
        $sources = implode(',', config('folem.sources_connaissance', []));

        return [
            'nom' => ['required', 'string', 'max:100'],
            'prenom' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email:rfc', 'max:150'],
            // Format attendu par LigdiCash : 226 suivi de 8 chiffres.
            'telephone' => ['required', 'regex:/^226[0-9]{8}$/'],
            'structure' => ['nullable', 'string', 'max:150'],
            'fonction' => ['nullable', 'string', 'max:150'],
            'secteur_activite' => ['required', 'in:'.$secteurs],

            'region_id' => ['required', 'integer', 'exists:regions,id'],
            'profil_id' => ['required', 'integer', 'exists:profils,id'],
            'jours' => ['required', 'array', 'min:1'],
            'jours.*' => ['in:'.$joursValides],
            'besoins_particuliers' => ['nullable', 'string', 'max:500'],
            'source_connaissance' => ['nullable', 'in:'.$sources],

            'accepte_conditions' => ['accepted'],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function messages(): array
    {
        return [
            'telephone.regex' => 'Le numéro doit être au format 226XXXXXXXX, sans espace ni signe plus.',
            'jours.required' => 'Choisissez au moins un jour de participation.',
            'accepte_conditions.accepted' => 'Merci de confirmer que vos informations sont exactes.',
            'region_id.required' => 'Sélectionnez votre région.',
            'profil_id.required' => 'Sélectionnez votre profil de participation.',
            'secteur_activite.required' => "Sélectionnez votre secteur d'activité.",
        ];
    }

    /**
     * @return array<string, string>
     */
    protected function validationAttributes(): array
    {
        return [
            'secteur_activite' => "secteur d'activité",
            'region_id' => 'région',
            'profil_id' => 'profil',
            'besoins_particuliers' => 'besoins particuliers',
            'source_connaissance' => 'origine',
        ];
    }

    /**
     * Champs contrôlés à chaque étape.
     *
     * @return array<int, array<int, string>>
     */
    protected function champsParEtape(): array
    {
        return [
            1 => ['nom', 'prenom', 'email', 'telephone', 'structure', 'fonction', 'secteur_activite'],
            2 => ['region_id', 'profil_id', 'jours', 'jours.*', 'besoins_particuliers', 'source_connaissance'],
            3 => ['accepte_conditions'],
        ];
    }

    #[Computed]
    public function regions()
    {
        return Region::orderBy('ordre')->orderBy('nom')->get();
    }

    #[Computed]
    public function profils()
    {
        return Profil::orderBy('ordre')->orderBy('nom')->get();
    }

    /**
     * Journées du forum, déduites des dates de l'édition.
     *
     * @return array<int, array{valeur: string, libelle: string, jour: string}>
     */
    #[Computed]
    public function joursForum(): array
    {
        if (! $this->edition->date_debut || ! $this->edition->date_fin) {
            return [];
        }

        $jours = [];
        $curseur = $this->edition->date_debut->copy();

        while ($curseur->lte($this->edition->date_fin)) {
            $jours[] = [
                'valeur' => $curseur->toDateString(),
                'libelle' => $curseur->translatedFormat('j F'),
                'jour' => $curseur->translatedFormat('l'),
            ];
            $curseur->addDay();
        }

        return $jours;
    }

    /**
     * Tarif applicable à la combinaison région + profil choisie.
     * Renvoie null tant que la sélection est incomplète ou non tarifée.
     */
    #[Computed]
    public function tarifCalcule(): ?int
    {
        if (! $this->region_id || ! $this->profil_id) {
            return null;
        }

        return $this->edition->tarifPour($this->region_id, $this->profil_id);
    }

    public function etapeSuivante(): void
    {
        $this->validate(
            $this->reglesEtape($this->etape),
            $this->messages(),
            $this->validationAttributes()
        );

        // On refuse d'avancer si la combinaison choisie n'a pas de tarif.
        if ($this->etape === 2 && $this->tarifCalcule() === null) {
            $this->addError('profil_id', "Aucun tarif n'est proposé pour cette combinaison région et profil. Contactez l'organisation.");

            return;
        }

        $this->etape = min($this->etape + 1, self::DERNIERE_ETAPE);
    }

    public function etapePrecedente(): void
    {
        $this->resetErrorBag();
        $this->etape = max($this->etape - 1, 1);
    }

    public function allerA(int $etape): void
    {
        // Navigation arrière uniquement : on ne saute pas une validation.
        if ($etape < $this->etape) {
            $this->resetErrorBag();
            $this->etape = $etape;
        }
    }

    public function submit(): void
    {
        // Le leurre est rempli : soumission automatisée, on s'arrête là.
        if ($this->site_web !== '') {
            return;
        }

        $cle = 'inscription:'.request()->ip();

        if (RateLimiter::tooManyAttempts($cle, 5)) {
            throw ValidationException::withMessages([
                'nom' => 'Trop de tentatives depuis cet appareil. Réessayez dans quelques minutes.',
            ]);
        }

        // Revalidation intégrale : les étapes précédentes ne font pas foi.
        $this->validate($this->rules(), $this->messages(), $this->validationAttributes());

        // Le montant est toujours relu en base, jamais pris depuis le navigateur.
        $montant = $this->edition->tarifPour($this->region_id, $this->profil_id);

        if ($montant === null) {
            $this->etape = 2;
            $this->addError('profil_id', "Aucun tarif n'est disponible pour cette combinaison. Contactez l'organisation.");

            return;
        }

        RateLimiter::hit($cle, 600);

        $inscription = DB::transaction(function () use ($montant) {
            $participant = Participant::firstOrCreate(
                ['email' => $this->email, 'telephone' => $this->telephone],
                [
                    'nom' => $this->nom,
                    'prenom' => $this->prenom,
                    'structure' => $this->structure ?: null,
                    'fonction' => $this->fonction ?: null,
                    'secteur_activite' => $this->secteur_activite,
                    'user_id' => auth()->id(),
                ]
            );

            // Une seule inscription par participant et par édition.
            $existante = Inscription::where('edition_id', $this->edition->id)
                ->where('participant_id', $participant->id)
                ->first();

            if ($existante) {
                return $existante;
            }

            return Inscription::create([
                'edition_id' => $this->edition->id,
                'participant_id' => $participant->id,
                'region_id' => $this->region_id,
                'profil_id' => $this->profil_id,
                'montant' => $montant,
                'statut' => 'pending',
                'jours_participation' => array_values($this->jours),
                'besoins_particuliers' => $this->besoins_particuliers ?: null,
                'source_connaissance' => $this->source_connaissance ?: null,
            ]);
        });

        $this->codeGenere = $inscription->code_inscription;
        $this->montantRegle = $inscription->montant;
        $this->inscriptionTerminee = true;
    }

    /**
     * Extrait de l'ensemble des règles celles qui concernent une étape.
     *
     * @return array<string, mixed>
     */
    protected function reglesEtape(int $etape): array
    {
        $champs = $this->champsParEtape()[$etape] ?? [];

        return array_intersect_key($this->rules(), array_flip($champs));
    }

    public function render()
    {
        return view('livewire.registration.register-form')
            ->layout('components.public-layout', [
                'title' => 'Inscription',
                'edition' => $this->edition,
                'ancres' => false,
            ]);
    }
}
