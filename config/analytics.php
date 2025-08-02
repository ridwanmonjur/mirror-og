<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Google Analytics Configuration
    |--------------------------------------------------------------------------
    */

    'ga4' => [
        'property_id' => env('GA4_PROPERTY_ID'),
        'credentials_path' => env('GA4_CREDENTIALS_PATH'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    */

    'cache' => [
        'enabled' => env('ANALYTICS_CACHE_ENABLED', true),
        'minutes' => env('ANALYTICS_CACHE_MINUTES', 5),
        'prefix' => 'analytics_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    */

    'rate_limit' => [
        'requests_per_hour' => env('ANALYTICS_RATE_LIMIT', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Metrics and Dimensions
    |--------------------------------------------------------------------------
    */

    'default_metrics' => [
        'realtime' => [
            'activeUsers',
            'screenPageViews',
            'eventCount',
            'conversions',
        ],
        'standard' => [
            'activeUsers',
            'newUsers',
            'sessions',
            'bounceRate',
            'averageSessionDuration',
            'screenPageViews',
        ],
    ],

    'default_dimensions' => [
        'realtime' => [
            'unifiedScreenName',
            'eventName',
            'country',
            'deviceCategory',
        ],
        'standard' => [
            'date',
            'country',
            'deviceCategory',
            'operatingSystem',
            'browser',
        ],
    ],
];
