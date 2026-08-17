<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReceiptController extends Controller
{
    /**
     * Recherche d'un dossier à partir du code d'inscription.
     *
     * Le message d'erreur reste volontairement identique quel que soit le cas
     * de figure : un code inexistant et un code mal formé donnent la même
     * réponse, pour ne pas aider à deviner des codes valides. La route est
     * par ailleurs limitée en fréquence.
     */
    public function rechercher(Request $request): RedirectResponse
    {
        $donnees = $request->validate([
            'code' => ['required', 'string', 'max:20', 'regex:/^[A-Za-z]{3}-[A-Za-z0-9]{4,12}$/'],
        ], [
            'code.required' => "Saisissez votre code d'inscription.",
            'code.regex' => 'Ce code ne correspond à aucun dossier.',
        ]);

        $code = strtoupper(trim($donnees['code']));

        $inscription = Inscription::where('code_inscription', $code)->first();

       if (! $inscription) {
        return redirect(url()->previous() . '#retrouver-dossier')
            ->withInput()
            ->withErrors(['code' => 'Ce code ne correspond à aucun dossier.']);
        }

        if (! $inscription->estPayee()) {
            return redirect()->route('paiement.initier', $inscription->code_inscription);
        }

        return redirect()->route('recu.telecharger', $inscription->code_inscription);
    }

    /**
     * Téléchargement du récépissé PDF d'une inscription payée.
     */
    public function telecharger(string $code): StreamedResponse
    {
        $inscription = Inscription::where('code_inscription', strtoupper($code))
            ->where('statut', 'paid')
            ->with('recu')
            ->firstOrFail();

        abort_unless($inscription->recu, 404, 'Récépissé non encore généré.');

        $disque = Storage::disk('public');

        abort_unless($disque->exists($inscription->recu->chemin_pdf), 404, 'Fichier du récépissé introuvable.');

        return $disque->download(
            $inscription->recu->chemin_pdf,
            "recepisse-{$inscription->code_inscription}.pdf"
        );
    }
}
