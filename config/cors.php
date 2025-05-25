<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'password/reset', 'password/email'],

    'allowed_methods' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'],

    'allowed_origins' => ['*'],
    // 'allowed_origins' => ['http://localhost:5173', 'https://gc-co-ed.vercel.app'],


    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'X-CSRF-TOKEN',
        'X-Requested-With',
        'Content-Type',
        'Accept',
        'Authorization',
        'Origin',
        'X-XSRF-TOKEN'
    ],

    'exposed_headers' => ['Set-Cookie'],

    'max_age' => 0,

    'supports_credentials' => true,

    'same_site' => 'none',
];
