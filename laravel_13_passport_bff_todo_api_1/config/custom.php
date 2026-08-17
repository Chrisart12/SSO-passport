<?php

return [
    'url' => env('APP_URL'),
    'oauth_url' => env('OAUTH_URL'),
    'frontend_url' => env('FRONTEND_URL'),
    'oauth' => [
        'client_id' => env('CLIENT_ID'),
        'client_secret' => env('CLIENT_SECRET'),
        'redirect_uri' => env('REDIRECT_URI')
    ],
    'pagination' => [
        'per_page' => env('PAGINATION_PER_PAGE', 10),
    ],
];