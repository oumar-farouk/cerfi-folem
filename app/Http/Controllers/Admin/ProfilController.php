<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profil;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProfilController extends Controller
{
    public function index()
    {
        $profils = Profil::withCount('inscriptions')->orderBy('ordre')->orderBy('nom')->get();

        return view('admin.profils.index', compact('profils'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom' => ['required', 'string', 'max:150', 'unique:profils,nom'],
            'ordre' => ['nullable', 'integer'],
        ]);

        Profil::create($request->only('nom', 'ordre'));

        return back()->with('success', 'Profil ajouté.');
    }

    public function update(Request $request, Profil $profil): RedirectResponse
    {
        $request->validate([
            'nom' => ['required', 'string', 'max:150', 'unique:profils,nom,'.$profil->id],
            'ordre' => ['nullable', 'integer'],
        ]);

        $profil->update($request->only('nom', 'ordre'));

        return back()->with('success', 'Profil mis à jour.');
    }

    public function destroy(Profil $profil): RedirectResponse
    {
        abort_if(
            $profil->prix()->exists() || $profil->inscriptions()->exists(),
            422,
            'Ce profil est utilisé par une grille tarifaire ou par des inscriptions existantes, il ne peut pas être supprimé.'
        );
        $profil->delete();

        return back()->with('success', 'Profil supprimé.');
    }
}
