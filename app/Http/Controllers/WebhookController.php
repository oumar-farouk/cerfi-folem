<?php

namespace App\Http\Controllers;

use App\Models\Paiement;
use App\Services\LigdiCashService;
use App\Services\RecuService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        protected LigdiCashService $ligdiCash,
        protected RecuService $recuService,
    ) {}

    /**
     * Callback appelé par LigdiCash après un paiement.
     *
     * C'est la source de vérité : la page de retour navigateur peut être
     * manquée si l'utilisateur ferme son onglet. Trois garanties ici :
     *
     *  1. On ne fait jamais confiance au contenu du payload. Le statut est
     *     revérifié auprès de l'API avant toute écriture.
     *  2. Le traitement est idempotent : LigdiCash peut rejouer le callback,
     *     un paiement déjà confirmé ressort en succès sans double génération
     *     de récépissé ni double marquage.
     *  3. La réponse est toujours 200, même en cas de rejet, pour éviter que
     *     l'opérateur ne relance indéfiniment un callback impossible à traiter.
     */
    public function ligdicash(Request $request): JsonResponse
    {
        $payload = $request->all();

        // On journalise sans les éventuels secrets présents dans le corps.
        Log::info('LigdiCash webhook reçu', ['token' => $payload['token'] ?? $payload['invoiceToken'] ?? null]);

        $token = $payload['token'] ?? $payload['invoiceToken'] ?? null;

        if (! $token || ! is_string($token)) {
            Log::warning('LigdiCash webhook sans token exploitable');

            return response()->json(['status' => 'ignored'], 200);
        }

        /*
        | Le paiement est retrouvé par son jeton de transaction, et par lui
        | seul. La version précédente ajoutait un `orWhere` non groupé sur
        | l'identifiant d'inscription transmis dans le payload : la condition
        | s'échappait du `when`, et un payload forgé pouvait faire remonter le
        | paiement d'une autre inscription.
        */
        $paiement = Paiement::where('token_transaction', $token)->latest()->first();

        if (! $paiement) {
            Log::warning('LigdiCash webhook : paiement introuvable', ['token' => $token]);

            return response()->json(['status' => 'not_found'], 200);
        }

        $paiement->update(['payload_callback' => $payload]);

        // Rejeu d'un callback déjà traité : rien à refaire.
        if ($paiement->statut === 'completed' && $paiement->inscription?->estPayee()) {
            return response()->json(['status' => 'already_processed'], 200);
        }

        $resultat = $this->ligdiCash->confirmerPaiement($token);

        $confirme = ($resultat['response_code'] ?? null) === '00'
            && ($resultat['status'] ?? null) === 'completed';

        if ($confirme) {
            DB::transaction(function () use ($paiement, $resultat) {
                $paiement->update([
                    'statut' => 'completed',
                    'operateur' => $resultat['operator'] ?? $resultat['payment_method'] ?? null,
                    'reference_ligdicash' => $resultat['transaction_id'] ?? null,
                    'payload_confirmation' => $resultat,
                    'confirmed_at' => now(),
                ]);

                $inscription = $paiement->inscription;

                if ($inscription && ! $inscription->estPayee()) {
                    $inscription->marquerCommePayee();
                }

                // Le service ne régénère pas un récépissé déjà émis.
                if ($inscription) {
                    $this->recuService->genererPour($inscription->fresh());
                }
            });

            return response()->json(['status' => 'ok'], 200);
        }

        if (($resultat['status'] ?? null) === 'failed') {
            $paiement->update(['statut' => 'failed', 'payload_confirmation' => $resultat]);
        }

        return response()->json(['status' => 'pending'], 200);
    }
}
