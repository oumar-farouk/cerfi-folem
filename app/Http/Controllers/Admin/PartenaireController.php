<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Partenaire;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PartenaireController extends Controller
{
    public function index(Edition $edition)
    {
        $partenaires = $edition->partenaires;

        return view('admin.partenaires.index', compact('edition', 'partenaires'));
    }

    public function create(Edition $edition)
    {
        return view('admin.partenaires.form', ['edition' => $edition, 'partenaire' => new Partenaire]);
    }

    public function store(Request $request, Edition $edition): RedirectResponse
    {
        $data = $this->validated($request);
        $data['logo'] = $request->file('logo')->store('partenaires', 'public');

        $edition->partenaires()->create($data);

        return redirect()->route('admin.editions.partenaires.index', $edition)->with('success', 'Partenaire ajouté.');
    }

    public function edit(Partenaire $partenaire)
    {
        return view('admin.partenaires.form', ['edition' => $partenaire->edition, 'partenaire' => $partenaire]);
    }

    public function update(Request $request, Partenaire $partenaire): RedirectResponse
    {
        $data = $this->validated($request, obligatoireLogo: false);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('partenaires', 'public');
        }

        $partenaire->update($data);

        return redirect()->route('admin.editions.partenaires.index', $partenaire->edition)->with('success', 'Partenaire mis à jour.');
    }

    public function destroy(Partenaire $partenaire): RedirectResponse
    {
        $edition = $partenaire->edition;
        $partenaire->delete();

        return redirect()->route('admin.editions.partenaires.index', $edition)->with('success', 'Partenaire supprimé.');
    }

    protected function validated(Request $request, bool $obligatoireLogo = true): array
    {
        return $request->validate([
            'nom' => ['required', 'string', 'max:150'],
            'type' => ['required', 'in:partenaire,sponsor'],
            'niveau' => ['nullable', 'string', 'max:100'],
            'lien_site' => ['nullable', 'url'],
            'ordre' => ['nullable', 'integer'],
            'logo' => [$obligatoireLogo ? 'required' : 'nullable', 'image', 'max:2048'],
        ]);
    }
}
