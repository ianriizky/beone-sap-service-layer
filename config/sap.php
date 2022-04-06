<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base URL
    |--------------------------------------------------------------------------
    |
    | Set the base URL used on the SAP Service Layer.
    |
    */

    'base_url' => env('SAP_BASE_URL'),

    /*
    |--------------------------------------------------------------------------
    | SSL Certificate Verification
    |--------------------------------------------------------------------------
    |
    | Determine the configuration of SSL certificate verification from guzzlehttp/guzzle.
    |
    | Note: If your Laravel application environment is in "local" state, then this
    |       configuration value will be automatically set as "false".
    |
    | @see https://docs.guzzlephp.org/en/stable/request-options.html#verify
    |
    */

    'ssl_verify' => env('SAP_SSL_VERIFY'),

    /*
    |--------------------------------------------------------------------------
    | Company DB
    |--------------------------------------------------------------------------
    |
    | Set the company database name that will be choosen on the SAP Service Layer.
    |
    */

    'company_db' => env('SAP_COMPANY_DB'),

    /*
    |--------------------------------------------------------------------------
    | Credentials
    |--------------------------------------------------------------------------
    |
    | Username and password for the authentication purpose when login into SAP Service Layer.
    |
    */

    'username' => env('SAP_USERNAME'),

    'password' => env('SAP_PASSWORD'),

    /*
    |--------------------------------------------------------------------------
    | Laravel HTTP Client Configuration
    |--------------------------------------------------------------------------
    |
    | Here is the list of value that will be used on the Laravel HTTP Client Configuration.
    |
    */

    'request_retry_times' => env('SAP_REQUEST_RETRY_TIMES', 2),

    'request_retry_sleep' => env('SAP_REQUEST_RETRY_SLEEP', 0),

    /*
    |--------------------------------------------------------------------------
    | Guzzle Request Configuration
    |--------------------------------------------------------------------------
    |
    | Here is the list of value that will be used on the Guzzle Request Configuration.
    |
    | @see https://docs.guzzlephp.org/en/stable/request-options.html
    |
    */

    'guzzle_options' => [],
];
