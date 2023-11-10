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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'google' => [
        'client_id' => '646564419111-qrjhqfommnl1oakv14kigb5eqcjmeh4o.apps.googleusercontent.com',
        'client_secret' => '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',
        'redirect' => env('MAIL_REDIRECT_URL'),
        'allowed_hosts' => [
            'driftwood.gg',
          ]
    ],


    'steam' => [
        'client_id' => null,
        'client_secret' => env('8AE02B51B52FA9BD6BB683EB4585A651'),
        'redirect' => env('http://localhost:8000/laravel-socialite/public/login/steam/callback'),
        'allowed_hosts' => [
          'driftwood.gg',
        ]
      ],

];
