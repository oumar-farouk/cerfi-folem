<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EditionController extends Controller
{
    public function index()
    {
        $editions = Edition::withCount('inscriptions')->orderByDesc('annee')->get();

        return view('admin.editions.index', compact('editions'));
    }

    public function create()
    {
        return view('admin.editions.form', ['edition' => new Edition]);
    }

    public function store(Request $request): RedirectResponse
    {
        Edition::create($this->validated($request));

        return redirect()->route('admin.editions.index')->with('success', 'Édition créée.');
    }

    public function edit(Edition $edition)
    {
        return view('admin.editions.form', compact('edition'));
    }

    public function update(Request $request, Edition $edition): RedirectResponse
    {
        $edition->update($this->validated($request));

        return redirect()->route('admin.editions.index')->with('success', 'Édition mise à jour.');
    }

    public function destroy(Edition $edition): RedirectResponse
    {
        abort_if($edition->inscriptions()->exists(), 422, 'Impossible de supprimer une édition ayant des inscriptions. Archivez-la plutôt.');
        $edition->delete();

        return redirect()->route('admin.editions.index')->with('success', 'Édition supprimée.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'nom' => ['required', 'string', 'max:150'],
            'theme' => ['nullable', 'string', 'max:255'],
            'annee' => ['required', 'integer', 'min:2020', 'max:2100'],
            'date_debut' => ['required', 'date'],
            'date_fin' => ['required', 'date', 'after_or_equal:date_debut'],
            'lieu' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'statut' => ['required', 'in:draft,active,archived'],
            'montant_inscription' => ['nullable', 'integer', 'min:0'],
        ]);
    }
}
