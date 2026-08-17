<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GalerieController extends Controller
{
    public function index(Edition $edition)
    {
        $photos = $edition->getMedia('galerie');

        return view('admin.galerie.index', compact('edition', 'photos'));
    }

    public function store(Request $request, Edition $edition): RedirectResponse
    {
        $request->validate([
            'photos' => ['required', 'array', 'max:20'],
            'photos.*' => ['image', 'max:5120'], // 5 Mo par fichier
        ]);

        foreach ($request->file('photos', []) as $photo) {
            $edition->addMedia($photo)->toMediaCollection('galerie');
        }

        return redirect()->route('admin.editions.galerie.index', $edition)
            ->with('success', count($request->file('photos', [])).' photo(s) ajoutée(s).');
    }

    public function destroy(Edition $edition, int $media): RedirectResponse
    {
        $item = $edition->getMedia('galerie')->firstWhere('id', $media);
        abort_unless($item, 404);
        $item->delete();

        return redirect()->route('admin.editions.galerie.index', $edition)->with('success', 'Photo supprimée.');
    }
}
