<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Services\LigdiCashService;
use App\Services\RecuService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected LigdiCashService $ligdiCash,
        protected RecuService $recuService,
    ) {}

    /**
     * Lance le paiement pour une inscription donnée (via son code) et redirige
     * vers la page de paiement hébergée LigdiCash.
     */
    public function initier(string $code): RedirectResponse
    {
        $inscription = Inscription::where('code_inscription', $code)->firstOrFail();

        if ($inscription->estPayee()) {
            return redirect()->route('recu.telecharger', $code)
                ->with('info', 'Cette inscription est déjà payée.');
        }

        $urlPaiement = $this->ligdiCash->initierPaiement($inscription);

        return redirect()->away($urlPaiement);
    }

    /**
     * Page de retour après paiement (return_url). Sert uniquement d'affichage :
     * la validation réelle se fait via le webhook confirmé côté serveur.
     * On revérifie ici aussi, en filet de sécurité, au cas où le webhook
     * n'aurait pas encore été traité.
     */
    public function succes(Request $request): \Illuminate\View\View
    {
        $inscription = Inscription::where('code_inscription', $request->query('code'))->firstOrFail();

        if (! $inscription->estPayee()) {
            $paiement = $inscription->paiements()->latest()->first();

            if ($paiement?->token_transaction) {
                $resultat = $this->ligdiCash->confirmerPaiement($paiement->token_transaction);

                if (($resultat['response_code'] ?? null) === '00' && ($resultat['status'] ?? null) === 'completed') {
                    $paiement->update([
                        'statut' => 'completed',
                        'payload_confirmation' => $resultat,
                        'confirmed_at' => now(),
                    ]);
                    $inscription->marquerCommePayee();
                    $this->recuService->genererPour($inscription);
                }
            }
        }

        return view('paiement.succes', ['inscription' => $inscription->fresh()]);
    }

    public function annule(Request $request): \Illuminate\View\View
    {
        $inscription = Inscription::where('code_inscription', $request->query('code'))->firstOrFail();

        return view('paiement.annule', ['inscription' => $inscription]);
    }
}
