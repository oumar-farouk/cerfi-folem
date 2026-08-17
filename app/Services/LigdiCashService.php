<?php

namespace App\Services;

use App\Models\Inscription;
use App\Models\Paiement;
use Illuminate\Support\Facades\Log;
use Ligdicash\Ligdicash;

/**
 * Encapsule les interactions avec l'API LigdiCash (payin avec redirection).
 *
 * Flux :
 *  1. initierPaiement()  -> crée la facture LigdiCash, sauvegarde un Paiement "pending",
 *                            renvoie l'URL vers laquelle rediriger le participant.
 *  2. Le participant paie sur la page hébergée LigdiCash (Orange/Moov/Telecel Money).
 *  3. LigdiCash appelle notre callback_url (webhook) ET/OU redirige le participant
 *     vers return_url : dans les deux cas on DOIT revérifier le statut via confirmerPaiement()
 *     avant de considérer l'inscription comme payée (ne jamais faire confiance au seul retour navigateur).
 */
class LigdiCashService
{
    protected Ligdicash $client;

    public function __construct()
    {
        $this->client = new Ligdicash([
            'api_key' => config('ligdicash.api_key'),
            'auth_token' => config('ligdicash.auth_token'),
            // IMPORTANT : le SDK route 'platform' => 'test' vers https://test.ligdicash.com,
            // qui n'existe pas (404). Le seul domaine fonctionnel de l'API LigdiCash est
            // app.ligdicash.com, aussi bien pour les clés d'un projet "Test" que "Live".
            // On force donc toujours 'live' ici ; le vrai mode test/production dépend
            // uniquement des clés API renseignées dans .env (LIGDICASH_API_KEY / AUTH_TOKEN),
            // pas de ce paramètre.
            'platform' => 'live',
        ]);
    }

    public function initierPaiement(Inscription $inscription): string
    {
        $participant = $inscription->participant;
        $edition = $inscription->edition;

        $invoice = $this->client->Invoice([
            'currency' => config('ligdicash.currency'),
            'description' => "Inscription {$edition->nom} - {$participant->nom_complet}",
            'customer_firstname' => $participant->prenom,
            'customer_lastname' => $participant->nom,
            'customer_email' => $participant->email,
            'store_name' => config('ligdicash.store_name'),
            'store_website_url' => config('ligdicash.store_website_url'),
        ]);

        $invoice->addItem([
            'name' => "Inscription {$edition->nom}",
            'description' => $edition->theme ?? $edition->nom,
            'quantity' => 1,
            'unit_price' => $inscription->montant,
        ]);

        $response = $invoice->payWithRedirection([
            'return_url' => config('ligdicash.return_url')."?code={$inscription->code_inscription}",
            'cancel_url' => config('ligdicash.cancel_url')."?code={$inscription->code_inscription}",
            'callback_url' => config('ligdicash.callback_url'),
            'custom_data' => [
                'inscription_id' => $inscription->id,
                'code_inscription' => $inscription->code_inscription,
            ],
        ]);

        // Trace la tentative de paiement, même avant confirmation
        Paiement::create([
            'inscription_id' => $inscription->id,
            'statut' => 'pending',
            'montant' => $inscription->montant,
            'devise' => config('ligdicash.currency'),
            'payload_creation' => (array) $response,
            'token_transaction' => $response->token ?? null,
        ]);

        if (($response->response_code ?? null) !== '00') {
            Log::error('LigdiCash: échec création facture', ['response' => $response, 'inscription_id' => $inscription->id]);
            throw new \RuntimeException("Impossible d'initier le paiement LigdiCash.");
        }

        return $response->response_text; // URL de paiement à rediriger
    }

    /**
     * Revérifie le statut réel d'une transaction auprès de LigdiCash.
     * À appeler systématiquement avant de valider une inscription :
     * - depuis le webhook (callback_url)
     * - depuis la page de retour (return_url), en filet de sécurité
     */
    public function confirmerPaiement(string $token): array
    {
        $transaction = $this->client->getTransaction([
            'token' => $token,
            'type' => 'payin',
        ]);

        return (array) $transaction;
    }
}
