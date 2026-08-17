<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use Illuminate\Contracts\View\View;

class EditionController extends Controller
{
    /**
     * Page publique d'une édition. Les brouillons restent invisibles :
     * seules les éditions actives ou archivées sont consultables.
     */
    public function show(Edition $edition): View
    {
        abort_if($edition->statut === 'draft', 404);

        $edition->load(['programmes.intervenants', 'intervenants', 'partenaires']);

        return view('editions.show', compact('edition'));
    }
}
