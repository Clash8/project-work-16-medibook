<?php

/*
|--------------------------------------------------------------------------
| Configurazione CORS
|--------------------------------------------------------------------------
| Sono autorizzate a consumare le API le sole origini attese, ovvero il
| front-end React in sviluppo e l'eventuale dominio di produzione indicato
| nella variabile d'ambiente FRONTEND_URL.
*/

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    'allowed_origins' => array_filter([
        env('FRONTEND_URL', 'http://localhost:5173'),
        'http://localhost:5173',
        'http://127.0.0.1:5173',
    ]),
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
