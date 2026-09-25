<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HttpSmsGateway implements SmsGateway
{
    public function envoyer(string $numero, string $message): void
    {
        $apiKey = config('services.ikoddi.api_key');
        $groupId = config('services.ikoddi.group_id');

        if (! $apiKey || ! $groupId) {
            return; // passerelle non configurée — no-op silencieux
        }

        $baseUrl = config('services.ikoddi.base_url');

        $reponse = Http::withHeaders([
                'Content-Type' => 'application/json',
                'x-api-key' => $apiKey,
            ])
            ->post("{$baseUrl}{$groupId}/sms", [
                'sentTo' => [$this->normaliserNumero($numero)],
                'message' => $message,
                'from' => config('services.ikoddi.sender'),
                'smsBroadCast' => 'Notification CERFI-FOLEM',
                'countryNumberCode' => '226',
                'countryStringCode' => 'BF',
            ]);

        if ($reponse->failed()) {
            Log::error('Échec envoi SMS IKODDI', [
                'numero' => $numero,
                'statut_http' => $reponse->status(),
                'corps' => $reponse->body(),
            ]);
        }

        $reponse->throw();
    }

    /**
     * IKODDI attend le numéro complet avec l'indicatif international (226 + 8 chiffres).
     * On normalise ici pour toujours retourner ce format, que le numéro stocké en base
     * l'ait déjà (participant issu d'un ancien flux) ou non (format nu à 8 chiffres).
    */
    protected function normaliserNumero(string $numero): string
    {
        $numero = preg_replace('/\D/', '', $numero); // retire espaces/tirets éventuels

        return str_starts_with($numero, '226') ? $numero : '226'.$numero;
    }
}
