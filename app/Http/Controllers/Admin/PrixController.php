<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Profil;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PrixController extends Controller
{
    /**
     * Affiche la grille croisée région x profil avec les tarifs déjà paramétrés
     * (ou vides) pour l'édition, avec un formulaire pour tout enregistrer d'un coup.
     */
    public function index(Edition $edition)
    {
        $regions = Region::orderBy('ordre')->orderBy('nom')->get();
        $profils = Profil::orderBy('ordre')->orderBy('nom')->get();

        // Grille [region_id][profil_id] => montant
        $grille = $edition->prix()->get()->groupBy('region_id')->map(function ($parRegion) {
            return $parRegion->keyBy('profil_id')->map->montant;
        });

        return view('admin.prix.index', compact('edition', 'regions', 'profils', 'grille'));
    }

    /**
     * Enregistre toute la grille en une fois : un input par combinaison région x profil.
     * Les champs laissés vides suppriment le tarif existant (combinaison non proposée).
     */
    public function store(Request $request, Edition $edition): RedirectResponse
    {
        $request->validate([
            'montants' => ['required', 'array'],
            'montants.*.*' => ['nullable', 'integer', 'min:0'],
        ]);

        foreach ($request->input('montants', []) as $regionId => $parProfil) {
            foreach ($parProfil as $profilId => $montant) {
                if ($montant === null || $montant === '') {
                    $edition->prix()
                        ->where('region_id', $regionId)
                        ->where('profil_id', $profilId)
                        ->delete();

                    continue;
                }

                $edition->prix()->updateOrCreate(
                    ['region_id' => $regionId, 'profil_id' => $profilId],
                    ['montant' => (int) $montant]
                );
            }
        }

        return redirect()->route('admin.editions.prix.index', $edition)->with('success', 'Grille tarifaire enregistrée.');
    }
}
