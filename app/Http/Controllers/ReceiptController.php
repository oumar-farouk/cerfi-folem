<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ReceiptController extends Controller
{
    /**
     * Page "Payé / Récépissé" façon FOLEM : on saisit son code d'inscription
     * pour retrouver son dossier (payer si pas encore fait, ou télécharger le reçu).
     */
    public function rechercher(Request $request)
    {
        $request->validate(['code' => ['required', 'string']]);

        $inscription = Inscription::with(['participant', 'edition', 'recu'])
            ->where('code_inscription', strtoupper($request->code))
            ->first();

        if (! $inscription) {
            return redirect(url()->previous() . '#retrouver-dossier')
                ->withInput()
                ->withErrors(['code' => "Aucune inscription ne correspond à ce code."]);
        }

        return view('inscription.dossier', compact('inscription'));
    }

    public function telecharger(string $code)
    {
        $inscription = Inscription::where('code_inscription', $code)
            ->where('statut', 'paid')
            ->with('recu')
            ->firstOrFail();

        abort_unless($inscription->recu, 404, 'Récépissé non encore généré.');

        return Storage::disk('public')->download(
            $inscription->recu->chemin_pdf,
            "recepisse-{$inscription->code_inscription}.pdf"
        );
    }
}
