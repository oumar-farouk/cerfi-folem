<?php

namespace App\Http\Controllers;

use App\Models\Inscription;

class VerificationController extends Controller
{
    /**
     * Écran affiché après scan du QR code du récépissé. Conçu pour être lu en une seconde
     * par un agent à l'entrée : un seul état visuel dominant (vert = valide, rouge = refusé).
     */
    public function show(string $code, string $hash)
    {
        $inscription = Inscription::with(['participant', 'edition', 'region', 'profil'])
            ->where('code_inscription', $code)
            ->first();

        // Statuts possibles, du plus clair au plus rare :
        // introuvable / hash invalide (falsifié) / pas encore payé / valide
        if (! $inscription) {
            return view('verification.show', ['statut' => 'introuvable', 'code' => $code]);
        }

        $recu = $inscription->recu;

        if (! $recu || ! hash_equals($recu->hash_verification, $hash)) {
            return view('verification.show', ['statut' => 'invalide', 'code' => $code]);
        }

        if (! $inscription->estPayee()) {
            return view('verification.show', [
                'statut' => 'non_paye',
                'code' => $code,
                'inscription' => $inscription,
            ]);
        }

        return view('verification.show', [
            'statut' => 'valide',
            'code' => $code,
            'inscription' => $inscription,
        ]);
    }
}
