<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Intervenant;
use App\Models\Partenaire;
use App\Models\Programme;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;

class AccueilController extends Controller
{
    /**
     * Page d'accueil publique.
     *
     * Tout le contenu affiché provient du back-office : édition active,
     * programme, intervenants, partenaires et galerie. Les requêtes sont
     * groupées ici plutôt que dispersées dans les vues.
     */
    public function __invoke(): View
    {
        $editionActive = Edition::active()->first();

        return view('accueil', [
            'editionActive' => $editionActive,
            'archives' => Edition::archivees()->get(),
            'programmes' => $this->programmes($editionActive),
            'intervenants' => $this->intervenants($editionActive),
            'partenaires' => $this->partenaires($editionActive),
            'photos' => $this->photos($editionActive),
        ]);
    }

    protected function programmes(?Edition $edition): Collection
    {
        return $edition
            ? $edition->programmes()->with('intervenants')->get()
            : Programme::query()->whereRaw('1 = 0')->get();
    }

    protected function intervenants(?Edition $edition): Collection
    {
        return $edition
            ? $edition->intervenants()->orderBy('nom')->get()
            : Intervenant::query()->whereRaw('1 = 0')->get();
    }

    protected function partenaires(?Edition $edition): Collection
    {
        return $edition
            ? $edition->partenaires
            : Partenaire::query()->whereRaw('1 = 0')->get();
    }

    /**
     * Galerie mise en avant sur l'accueil : les photos de l'édition la plus
     * récente qui en possède, limitées à huit pour ne pas alourdir la page.
     */
    protected function photos(?Edition $edition): SupportCollection
    {
        $source = $edition?->getMedia('galerie');

        if (! $source || $source->isEmpty()) {
            $derniereAvecPhotos = Edition::archivees()
                ->get()
                ->first(fn (Edition $e) => $e->getMedia('galerie')->isNotEmpty());

            $source = $derniereAvecPhotos?->getMedia('galerie');
        }

        return collect($source ?? [])->take(8);
    }
}
