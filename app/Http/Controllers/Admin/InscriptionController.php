<?php

namespace App\Http\Controllers\Admin;

use App\Exports\InscriptionsExport;
use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Inscription;
use App\Models\Paiement;
use App\Notifications\PaiementRecu;
use App\Services\RecuService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class InscriptionController extends Controller
{
    public function index(Request $request): View
    {
        $edition = $request->filled('edition_id')
            ? Edition::findOrFail($request->integer('edition_id'))
            : Edition::active()->first();

        $requete = $this->requeteFiltree($request, $edition?->id);

        return view('admin.inscriptions.index', [
            'inscriptions' => (clone $requete)
                ->with(['participant', 'region', 'profil'])
                ->latest()
                ->paginate(25),
            'edition' => $edition,
            'editions' => Edition::orderByDesc('annee')->get(),
            'stats' => [
                'total' => (clone $requete)->count(),
                'payees' => (clone $requete)->payees()->count(),
                'ca' => (int) (clone $requete)->payees()->sum('montant'),
            ],
        ]);
    }

    /**
     * Fiche d'une inscription : dossier, historique des paiements et des
     * pointages, et actions autorisées selon le rôle (la vue masque les
     * boutons, les routes protègent réellement).
     */
    public function show(Inscription $inscription): View
    {
        $inscription->load([
            'participant', 'edition', 'region', 'profil', 'recu',
            'paiements' => fn ($q) => $q->latest(),
            'paiements.enregistrePar',
            'pointages' => fn ($q) => $q->orderBy('jour'),
            'pointages.agent',
        ]);

        return view('admin.inscriptions.show', [
            'inscription' => $inscription,
            'modesManuels' => array_intersect_key(Paiement::MODES, array_flip(Paiement::MODES_MANUELS)),
        ]);
    }

    public function renvoyerRecepisse(Inscription $inscription, RecuService $recuService): RedirectResponse
    {
        if (! $inscription->estPayee()) {
            return back()->with('erreur', "Seule une inscription payée dispose d'un récépissé.");
        }

        if (! $inscription->recu) {
            $recuService->genererPour($inscription);
        }

        $inscription->participant->notify(new PaiementRecu($inscription->fresh()));

        if (function_exists('activity')) {
            activity()
                ->performedOn($inscription)
                ->causedBy(auth()->user())
                ->withProperties(['code' => $inscription->code_inscription])
                ->log('Récépissé renvoyé');
        }

        return back()->with('succes', 'Récépissé renvoyé au participant.');
    }

    public function export(Request $request): BinaryFileResponse
    {
        $edition = $request->filled('edition_id')
            ? Edition::findOrFail($request->integer('edition_id'))
            : Edition::active()->first();

        $nomFichier = sprintf(
            'inscriptions-%s-%s.xlsx',
            $edition?->slug ?? 'toutes-editions',
            now()->format('Y-m-d')
        );

        return Excel::download(
            new InscriptionsExport(
                editionId: $edition?->id,
                statut: $request->string('statut')->toString() ?: null,
                recherche: $request->string('q')->toString() ?: null,
            ),
            $nomFichier
        );
    }

    /**
     * Construit la requête de base commune à la liste, aux compteurs et à l'export,
     * pour que l'écran affiché et le fichier téléchargé portent exactement sur le
     * même périmètre.
     */
    protected function requeteFiltree(Request $request, ?int $editionId): Builder
    {
        $recherche = $request->string('q')->trim()->toString();

        return Inscription::query()
            ->when($editionId, fn (Builder $q) => $q->where('edition_id', $editionId))
            ->when($request->filled('statut'), function (Builder $q) use ($request) {
                $statut = $request->string('statut')->toString();

                // On n'accepte que les statuts connus : le paramètre vient de l'URL.
                if (array_key_exists($statut, Inscription::STATUTS)) {
                    $q->where('statut', $statut);
                }
            })
            ->when($recherche !== '', function (Builder $q) use ($recherche) {
                $q->where(function (Builder $sous) use ($recherche) {
                    $sous->where('code_inscription', 'like', "%{$recherche}%")
                        ->orWhereHas('participant', function (Builder $p) use ($recherche) {
                            $p->where('nom', 'like', "%{$recherche}%")
                                ->orWhere('prenom', 'like', "%{$recherche}%")
                                ->orWhere('email', 'like', "%{$recherche}%")
                                ->orWhere('telephone', 'like', "%{$recherche}%")
                                ->orWhere('structure', 'like', "%{$recherche}%");
                        });
                });
            });
    }
}
