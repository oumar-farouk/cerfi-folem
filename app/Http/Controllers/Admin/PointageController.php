<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Inscription;
use App\Models\Pointage;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PointageController extends Controller
{
    public const MESSAGES = [
        'ok' => 'Entrée autorisée',
        'deja_pointe' => "Déjà pointé aujourd'hui",
        'non_paye' => 'Inscription non payée : accès refusé',
        'hors_jour' => "Cette inscription n'est pas valable aujourd'hui",
        'invalide' => 'QR code invalide ou falsifié',
        'introuvable' => 'Code introuvable',
        'autre_edition' => "Inscription d'une autre édition",
    ];

    public function index(): View
    {
        $edition = Edition::active()->first();
        $jour = today();

        $recents = Pointage::with('inscription.participant')
            ->whereDate('jour', $jour)
            ->when($edition, fn ($q) => $q->whereHas('inscription', fn ($i) => $i->where('edition_id', $edition->id)))
            ->latest('pointe_a')
            ->limit(15)
            ->get()
            ->map(fn (Pointage $p) => $this->ligne($p))
            ->values();

        $pointes = Pointage::whereDate('jour', $jour)
            ->when($edition, fn ($q) => $q->whereHas('inscription', fn ($i) => $i->where('edition_id', $edition->id)))
            ->count();

        $attendus = Inscription::payees()
            ->when($edition, fn ($q) => $q->where('edition_id', $edition->id))
            ->get(['id', 'jours_participation'])
            ->filter(fn (Inscription $i) => $this->attendueCeJour($i, $jour))
            ->count();

        return view('admin.pointage.index', compact('edition', 'jour', 'recents', 'pointes', 'attendus'));
    }

    /**
     * Reçoit soit le contenu du QR code (URL /verification/{code}/{hash}),
     * soit un code saisi à la main (FLM-XXXXXX). Répond en JSON pour l'écran
     * de scan, ou par un retour de page si JavaScript est indisponible.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $donnees = $request->validate([
            'code' => ['required', 'string', 'max:255'],
            'methode' => ['nullable', 'in:scan,manuel'],
        ]);

        [$code, $hash] = $this->extraire($donnees['code']);

        $inscription = Inscription::with(['participant', 'recu', 'edition'])
            ->where('code_inscription', $code)
            ->first();

        if (! $inscription) {
            return $this->reponse($request, 'introuvable', $code);
        }

        // Un hash n'est présent que pour un scan : on le contrôle comme le fait
        // VerificationController. Une saisie manuelle n'a pas de hash à vérifier.
        if ($hash !== null && (! $inscription->recu || ! hash_equals($inscription->recu->hash_verification, $hash))) {
            return $this->reponse($request, 'invalide', $code);
        }

        if (! $inscription->estPayee()) {
            return $this->reponse($request, 'non_paye', $code, $inscription);
        }

        $editionActiveId = Edition::active()->value('id');
        if ($editionActiveId && $inscription->edition_id !== $editionActiveId) {
            return $this->reponse($request, 'autre_edition', $code, $inscription);
        }

        $jour = today();

        if (! $this->attendueCeJour($inscription, $jour)) {
            return $this->reponse($request, 'hors_jour', $code, $inscription);
        }

        // createOrFirst s'appuie sur l'index unique (inscription, jour) :
        // deux scans simultanés ne peuvent pas créer deux pointages.
        $pointage = Pointage::createOrFirst(
            ['inscription_id' => $inscription->id, 'jour' => $jour->toDateString()],
            [
                'pointe_a' => now(),
                'pointe_par' => $request->user()->id,
                'methode' => $donnees['methode'] ?? 'scan',
            ],
        );
        $pointage->setRelation('inscription', $inscription);

        return $this->reponse(
            $request,
            $pointage->wasRecentlyCreated ? 'ok' : 'deja_pointe',
            $code,
            $inscription,
            $pointage,
        );
    }

    /**
     * @return array{0: string, 1: string|null} [code, hash]
     */
    protected function extraire(string $saisie): array
    {
        $saisie = trim($saisie);

        if (preg_match('#/verification/([^/?\#\s]+)/([^/?\#\s]+)#', $saisie, $m)) {
            return [strtoupper(urldecode($m[1])), $m[2]];
        }

        return [strtoupper($saisie), null];
    }

    /**
     * Une inscription sans jours précisés vaut « tous les jours ».
     */
    protected function attendueCeJour(Inscription $inscription, Carbon $jour): bool
    {
        $jours = collect($inscription->jours_participation ?? [])
            ->map(fn ($j) => Carbon::parse($j)->toDateString());

        return $jours->isEmpty() || $jours->contains($jour->toDateString());
    }

    protected function ligne(Pointage $p): array
    {
        return [
            'code' => $p->inscription->code_inscription,
            'nom' => $p->inscription->participant->nom_complet,
            'heure' => $p->pointe_a->format('H:i'),
            'methode' => $p->methode,
        ];
    }

    protected function reponse(
        Request $request,
        string $etat,
        string $code,
        ?Inscription $inscription = null,
        ?Pointage $pointage = null,
    ): JsonResponse|RedirectResponse {
        $message = self::MESSAGES[$etat];

        if ($etat === 'deja_pointe' && $pointage) {
            $message .= ' à '.$pointage->pointe_a->format('H:i');
        }

        $payload = [
            'etat' => $etat,
            'ok' => $etat === 'ok',
            'message' => $message,
            'code' => $inscription?->code_inscription ?? $code,
            'participant' => $inscription?->participant?->nom_complet,
            'structure' => $inscription?->participant?->structure,
            'jours' => $inscription?->joursFormates(),
            'ligne' => ($etat === 'ok' && $pointage) ? $this->ligne($pointage) : null,
        ];

        return $request->expectsJson()
            ? response()->json($payload)
            : back()->with('pointage', $payload);
    }
}
