<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenObserve Enabled
    |--------------------------------------------------------------------------
    |
    | This option controls whether the OpenObserve logging is enabled.
    |
    */
    'enabled' => env('OPENOBSERVE_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | OpenObserve URL
    |--------------------------------------------------------------------------
    |
    | The URL of your OpenObserve instance.
    |
    */
    'url' => env('OPENOBSERVE_URL', 'http://localhost:5080'),

    /*
    |--------------------------------------------------------------------------
    | Organization
    |--------------------------------------------------------------------------
    |
    | The organization name in OpenObserve.
    |
    */
    'organization' => env('OPENOBSERVE_ORGANIZATION', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Stream Name
    |--------------------------------------------------------------------------
    |
    | The stream name where logs will be sent.
    |
    */
    'stream' => env('OPENOBSERVE_STREAM', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    |
    | Your OpenObserve authentication credentials.
    |
    */
    'auth' => [
        'email' => env('OPENOBSERVE_EMAIL'),
        'password' => env('OPENOBSERVE_PASSWORD'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Batch Size
    |--------------------------------------------------------------------------
    |
    | Number of log entries to batch before sending to OpenObserve.
    |
    */
    'batch_size' => env('OPENOBSERVE_BATCH_SIZE', 100),

    /*
    |--------------------------------------------------------------------------
    | Timeout
    |--------------------------------------------------------------------------
    |
    | HTTP request timeout in seconds.
    |
    */
    'timeout' => env('OPENOBSERVE_TIMEOUT', 5),

    /*
    |--------------------------------------------------------------------------
    | SSL Verify
    |--------------------------------------------------------------------------
    |
    | Whether to verify SSL certificates.
    |
    */
    'ssl_verify' => env('OPENOBSERVE_SSL_VERIFY', true),

    /*
    |--------------------------------------------------------------------------
    | Additional Fields
    |--------------------------------------------------------------------------
    |
    | Additional fields to include with every log entry.
    |
    */
    'additional_fields' => [
        'environment' => env('APP_ENV', 'production'),
        'application' => env('APP_NAME', 'Laravel'),
    ],
];
