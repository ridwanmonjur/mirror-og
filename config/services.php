<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'firebase' => env('FIREBASE_CREDENTIALS'),
    'mail_address' => config('services.mail_address'),

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'mail' => config('services.mail_address'),

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URL'),
        'allowed_hosts' => [
            'driftwood.gg',
            'oceansgaming.gg',
        ],
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

    'steam' => [
        'client_id' => null,
        'client_secret' => '8AE02B51B52FA9BD6BB683EB4585A651',
        'redirect' => env('STEAM_REDIRECT_URL'),
        'allowed_hosts' => [
            'driftwood.gg',
            'oceansgaming.gg',
        ],
    ],

    'cloud_function' => [
        'url' => env('VITE_API_URL'),
    ],

];
