<?php

return [
    // Clés récupérées depuis le dashboard LigdiCash (projet API activé)
    'api_key' => env('LIGDICASH_API_KEY'),
    'auth_token' => env('LIGDICASH_AUTH_TOKEN'),

    // 'live' ou 'test' selon l'environnement
    'platform' => env('LIGDICASH_PLATFORM', 'test'),

    'currency' => 'XOF',

    // Infos boutique affichées sur la page de paiement hébergée LigdiCash
    'store_name' => env('LIGDICASH_STORE_NAME', 'FOLEM - CERFI'),
    'store_website_url' => env('APP_URL'),

    // URLs de callback (doivent être publiquement accessibles, donc pas en local
    // sauf tunnel type ngrok/expose pendant les tests)
    'return_url' => env('APP_URL').'/paiement/succes',
    'cancel_url' => env('APP_URL').'/paiement/annule',
    'callback_url' => env('APP_URL').'/webhooks/ligdicash',
];
