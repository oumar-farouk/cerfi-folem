<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index()
    {
        $regions = Region::withCount('inscriptions')->orderBy('ordre')->orderBy('nom')->get();

        return view('admin.regions.index', compact('regions'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom' => ['required', 'string', 'max:150', 'unique:regions,nom'],
            'ordre' => ['nullable', 'integer'],
        ]);

        Region::create($request->only('nom', 'ordre'));

        return back()->with('success', 'Région ajoutée.');
    }

    public function update(Request $request, Region $region): RedirectResponse
    {
        $request->validate([
            'nom' => ['required', 'string', 'max:150', 'unique:regions,nom,'.$region->id],
            'ordre' => ['nullable', 'integer'],
        ]);

        $region->update($request->only('nom', 'ordre'));

        return back()->with('success', 'Région mise à jour.');
    }

    public function destroy(Region $region): RedirectResponse
    {
        abort_if(
            $region->prix()->exists() || $region->inscriptions()->exists(),
            422,
            'Cette région est utilisée par une grille tarifaire ou par des inscriptions existantes, elle ne peut pas être supprimée.'
        );
        $region->delete();

        return back()->with('success', 'Région supprimée.');
    }
}
