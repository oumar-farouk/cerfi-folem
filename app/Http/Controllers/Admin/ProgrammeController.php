<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Intervenant;
use App\Models\Programme;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProgrammeController extends Controller
{
    public function index(Edition $edition)
    {
        $programmes = $edition->programmes()->with('intervenants')->get();

        return view('admin.programmes.index', compact('edition', 'programmes'));
    }

    public function create(Edition $edition)
    {
        $intervenants = $edition->intervenants;

        return view('admin.programmes.form', [
            'edition' => $edition,
            'programme' => new Programme,
            'intervenants' => $intervenants,
        ]);
    }

    public function store(Request $request, Edition $edition): RedirectResponse
    {
        $data = $this->validated($request);
        $programme = $edition->programmes()->create($data);
        $programme->intervenants()->sync($request->input('intervenants', []));

        return redirect()->route('admin.editions.programmes.index', $edition)->with('success', 'Session ajoutée au programme.');
    }

    public function edit(Programme $programme)
    {
        return view('admin.programmes.form', [
            'edition' => $programme->edition,
            'programme' => $programme,
            'intervenants' => $programme->edition->intervenants,
        ]);
    }

    public function update(Request $request, Programme $programme): RedirectResponse
    {
        $programme->update($this->validated($request));
        $programme->intervenants()->sync($request->input('intervenants', []));

        return redirect()->route('admin.editions.programmes.index', $programme->edition)->with('success', 'Session mise à jour.');
    }

    public function destroy(Programme $programme): RedirectResponse
    {
        $edition = $programme->edition;
        $programme->delete();

        return redirect()->route('admin.editions.programmes.index', $edition)->with('success', 'Session supprimée.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', 'string', 'max:100'],
            'date' => ['required', 'date'],
            'heure_debut' => ['required'],
            'heure_fin' => ['nullable'],
            'salle' => ['nullable', 'string', 'max:150'],
            'ordre' => ['nullable', 'integer'],
        ]);
    }
}
