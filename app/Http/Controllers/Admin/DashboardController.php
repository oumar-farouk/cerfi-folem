<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Inscription;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Tableau de bord du back-office.
     *
     * Toutes les statistiques sont calculées ici plutôt que dans la vue :
     * l'ancienne version faisait ses requêtes directement dans le Blade, ce qui
     * rendait la page impossible à tester et à mettre en cache.
     */
    public function __invoke(Request $request): View
    {
        $edition = $request->filled('edition_id')
            ? Edition::find($request->integer('edition_id'))
            : Edition::active()->first();

        $edition ??= Edition::orderByDesc('annee')->first();

        if (! $edition) {
            return view('admin.dashboard', [
                'edition' => null,
                'editions' => collect(),
                'stats' => null,
                'serieJours' => [],
                'parRegion' => [],
                'parProfil' => [],
                'dernieres' => collect(),
            ]);
        }

        $base = fn () => Inscription::where('edition_id', $edition->id);

        $total = $base()->count();
        $payees = $base()->where('statut', 'paid')->count();
        $enAttente = $base()->where('statut', 'pending')->count();
        $recettes = (int) $base()->where('statut', 'paid')->sum('montant');

        $stats = [
            'total' => $total,
            'payees' => $payees,
            'attente' => $enAttente,
            'recettes' => $recettes,
            'conversion' => $total > 0 ? round($payees / $total * 100, 1) : 0.0,
            'panier' => $payees > 0 ? (int) round($recettes / $payees) : 0,
        ];

        return view('admin.dashboard', [
            'edition' => $edition,
            'editions' => Edition::orderByDesc('annee')->get(),
            'stats' => $stats,
            'serieJours' => $this->serieTrenteJours($edition),
            'parRegion' => $this->repartition($edition, 'region'),
            'parProfil' => $this->repartition($edition, 'profil'),
            'dernieres' => Inscription::where('edition_id', $edition->id)
                ->with(['participant', 'region', 'profil'])
                ->latest()
                ->limit(8)
                ->get(),
        ]);
    }

    /**
     * Inscriptions créées et payées sur les trente derniers jours, jour par jour.
     * Les jours sans inscription sont remplis à zéro pour que la courbe reste continue.
     *
     * @return array{labels: array<int, string>, creees: array<int, int>, payees: array<int, int>}
     */
    protected function serieTrenteJours(Edition $edition): array
    {
        $debut = Carbon::today()->subDays(29);

        $creees = Inscription::where('edition_id', $edition->id)
            ->where('created_at', '>=', $debut)
            ->get()
            ->groupBy(fn ($i) => $i->created_at->toDateString())
            ->map->count();

        $payees = Inscription::where('edition_id', $edition->id)
            ->whereNotNull('paid_at')
            ->where('paid_at', '>=', $debut)
            ->get()
            ->groupBy(fn ($i) => $i->paid_at->toDateString())
            ->map->count();

        $labels = [];
        $serieCreees = [];
        $seriePayees = [];

        for ($jour = $debut->copy(); $jour->lte(Carbon::today()); $jour->addDay()) {
            $cle = $jour->toDateString();
            $labels[] = $jour->translatedFormat('d M');
            $serieCreees[] = (int) ($creees[$cle] ?? 0);
            $seriePayees[] = (int) ($payees[$cle] ?? 0);
        }

        return ['labels' => $labels, 'creees' => $serieCreees, 'payees' => $seriePayees];
    }

    /**
     * Répartition des inscriptions par région ou par profil, du plus au moins nombreux.
     *
     * @return array{labels: array<int, string>, valeurs: array<int, int>}
     */
    protected function repartition(Edition $edition, string $relation): array
    {
        $donnees = Inscription::where('edition_id', $edition->id)
            ->with($relation)
            ->get()
            ->groupBy(fn ($inscription) => $inscription->{$relation}?->nom ?? 'Non renseigné')
            ->map->count()
            ->sortDesc()
            ->take(10);

        return [
            'labels' => $donnees->keys()->all(),
            'valeurs' => $donnees->values()->map(fn ($v) => (int) $v)->all(),
        ];
    }
}
