<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Intervenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class IntervenantController extends Controller
{
    public function index(Edition $edition)
    {
        $intervenants = $edition->intervenants;

        return view('admin.intervenants.index', compact('edition', 'intervenants'));
    }

    public function create(Edition $edition)
    {
        return view('admin.intervenants.form', ['edition' => $edition, 'intervenant' => new Intervenant]);
    }

    public function store(Request $request, Edition $edition): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('intervenants', 'public');
        }

        $edition->intervenants()->create($data);

        return redirect()->route('admin.editions.intervenants.index', $edition)->with('success', 'Intervenant ajouté.');
    }

    public function edit(Intervenant $intervenant)
    {
        return view('admin.intervenants.form', ['edition' => $intervenant->edition, 'intervenant' => $intervenant]);
    }

    public function update(Request $request, Intervenant $intervenant): RedirectResponse
    {
        $data = $this->validated($request);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('intervenants', 'public');
        }

        $intervenant->update($data);

        return redirect()->route('admin.editions.intervenants.index', $intervenant->edition)->with('success', 'Intervenant mis à jour.');
    }

    public function destroy(Intervenant $intervenant): RedirectResponse
    {
        $edition = $intervenant->edition;
        $intervenant->delete();

        return redirect()->route('admin.editions.intervenants.index', $edition)->with('success', 'Intervenant supprimé.');
    }

    protected function validated(Request $request): array
    {
        return $request->validate([
            'nom' => ['required', 'string', 'max:150'],
            'fonction' => ['nullable', 'string', 'max:150'],
            'structure' => ['nullable', 'string', 'max:150'],
            'bio' => ['nullable', 'string'],
            'linkedin_url' => ['nullable', 'url'],
            'photo' => ['nullable', 'image', 'max:2048'],
        ]);
    }
}
