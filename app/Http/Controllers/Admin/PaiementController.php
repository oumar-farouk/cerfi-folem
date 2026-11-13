<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PaiementsExport;
use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Notifications\PaiementRecu;
use App\Services\RecuService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PaiementController extends Controller
{
    public function __construct(protected RecuService $recuService) {}

    /**
     * Point d'entrée de l'encaissement manuel : retrouver un dossier impayé
     * (par code, nom, téléphone…) puis ouvrir sa fiche sur le formulaire
     * d'encaissement. Sans recherche, on liste les derniers dossiers en attente.
     */
    public function encaisser(Request $request): View
    {
        $edition = $this->edition($request);
        $recherche = $request->string('q')->trim()->toString();

        $inscriptions = Inscription::query()
            ->with('participant')
            ->when($edition, fn ($q) => $q->where('edition_id', $edition->id))
            ->whereIn('statut', ['pending', 'expired'])
            ->when($recherche !== '', function ($q) use ($recherche) {
                $q->where(function ($sous) use ($recherche) {
                    $sous->where('code_inscription', 'like', "%{$recherche}%")
                        ->orWhereHas('participant', fn ($p) => $p
                            ->where('nom', 'like', "%{$recherche}%")
                            ->orWhere('prenom', 'like', "%{$recherche}%")
                            ->orWhere('telephone', 'like', "%{$recherche}%")
                            ->orWhere('email', 'like', "%{$recherche}%")
                            ->orWhere('structure', 'like', "%{$recherche}%"));
                });
            })
            ->latest()
            ->limit($recherche !== '' ? 30 : 15)
            ->get();

        return view('admin.paiements.encaisser', [
            'inscriptions' => $inscriptions,
            'edition' => $edition,
            'editions' => Edition::orderByDesc('annee')->get(),
        ]);
    }

    public function index(Request $request): View
    {
        $request->validate([
            'du' => ['nullable', 'date'],
            'au' => ['nullable', 'date'],
        ]);

        $edition = $this->edition($request);
        $filtres = $this->filtres($request, $edition?->id);

        $requete = Paiement::query()->filtres($filtres);

        $parMode = (clone $requete)
            ->where('statut', 'completed')
            ->selectRaw('mode, count(*) as nombre, sum(montant) as total')
            ->groupBy('mode')
            ->get()
            ->keyBy('mode');

        return view('admin.paiements.index', [
            'paiements' => (clone $requete)
                ->with(['inscription.participant', 'enregistrePar'])
                ->latest()
                ->paginate(25)
                ->withQueryString(),
            'edition' => $edition,
            'editions' => Edition::orderByDesc('annee')->get(),
            'statuts' => Paiement::STATUTS,
            'modes' => Paiement::MODES,
            'stats' => [
                'encaisse' => (int) $parMode->sum('total'),
                'nombre' => (int) $parMode->sum('nombre'),
                'par_mode' => $parMode,
            ],
        ]);
    }

    public function export(Request $request): BinaryFileResponse
    {
        $edition = $this->edition($request);

        $nomFichier = sprintf(
            'paiements-%s-%s.xlsx',
            $edition?->slug ?? 'toutes-editions',
            now()->format('Y-m-d')
        );

        return Excel::download(
            new PaiementsExport($this->filtres($request, $edition?->id)),
            $nomFichier
        );
    }

    /**
     * Enregistre un paiement hors ligne (espèces, virement, chèque…) et
     * déclenche la même suite que le webhook LigdiCash : inscription payée,
     * récépissé généré, participant notifié.
     */
    public function storeManuel(Request $request, Inscription $inscription): RedirectResponse
    {
        $donnees = $request->validate([
            'mode' => ['required', Rule::in(Paiement::MODES_MANUELS)],
            'reference_manuelle' => ['nullable', 'required_if:mode,virement,cheque', 'string', 'max:100'],
            'note' => ['nullable', 'string', 'max:500'],
        ], [
            'reference_manuelle.required_if' => 'Indiquez le numéro de virement ou de chèque.',
        ]);

        $paiement = DB::transaction(function () use ($inscription, $donnees, $request) {
            // Verrou : évite qu'un webhook LigdiCash et une saisie manuelle
            // valident la même inscription au même instant.
            $courante = Inscription::whereKey($inscription->id)->lockForUpdate()->firstOrFail();

            if ($courante->estPayee()) {
                throw ValidationException::withMessages(['mode' => 'Cette inscription est déjà payée.']);
            }

            if ($courante->statut === 'cancelled') {
                throw ValidationException::withMessages(['mode' => 'Cette inscription est annulée.']);
            }

            // Un paiement mobile money encore ouvert ne doit plus pouvoir
            // aboutir et payer une seconde fois.
            $courante->paiements()->where('statut', 'pending')->update(['statut' => 'cancelled']);

            $paiement = $courante->paiements()->create([
                'mode' => $donnees['mode'],
                'operateur' => Paiement::MODES[$donnees['mode']],
                'reference_manuelle' => $donnees['reference_manuelle'] ?? null,
                'statut' => 'completed',
                'montant' => $courante->montant,
                'devise' => 'XOF',
                'note' => $donnees['note'] ?? null,
                'enregistre_par' => $request->user()->id,
                'confirmed_at' => now(),
            ]);

            $courante->marquerCommePayee();

            return $paiement;
        });

        $inscription->refresh();

        // Le paiement est déjà acquis : une panne de PDF ou de SMS ne doit pas
        // faire échouer l'opération. On journalise, le récépissé pourra être renvoyé.
        try {
            $this->recuService->genererPour($inscription);
            $inscription->participant->notify(new PaiementRecu($inscription->fresh()));
        } catch (\Throwable $e) {
            Log::warning('Paiement manuel enregistré, récépissé ou notification en échec', [
                'inscription_id' => $inscription->id,
                'erreur' => $e->getMessage(),
            ]);

            return back()->with('succes', 'Paiement enregistré, mais le récépissé ou la notification a échoué. Utilisez « Renvoyer le récépissé ».');
        }

        if (function_exists('activity')) {
            activity()
                ->performedOn($paiement)
                ->causedBy($request->user())
                ->withProperties(['inscription' => $inscription->code_inscription, 'mode' => $paiement->mode])
                ->log('Paiement manuel enregistré');
        }

        return back()->with('succes', 'Paiement enregistré. Le récépissé a été envoyé au participant.');
    }

    protected function edition(Request $request): ?Edition
    {
        return $request->filled('edition_id')
            ? Edition::findOrFail($request->integer('edition_id'))
            : Edition::active()->first();
    }

    protected function filtres(Request $request, ?int $editionId): array
    {
        return [
            'edition_id' => $editionId,
            'statut' => $request->string('statut')->toString() ?: null,
            'mode' => $request->string('mode')->toString() ?: null,
            'q' => $request->string('q')->trim()->toString() ?: null,
            'du' => $request->string('du')->toString() ?: null,
            'au' => $request->string('au')->toString() ?: null,
        ];
    }
}
