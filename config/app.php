<?php
return [
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'allowed-url' => [
        'http://localhost:8080',
        'http://localhost:3000',
    ],
    'unguard-url' => [
        'login',
        'logout',
        'home',
        'policy',
        'news',
        'token-check',
        'search-user',
    ],
];
