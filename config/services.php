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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'immoscout' => [
        'base_url' => env('IMMOSCOUT_BASE_URL', 'https://rest.immobilienscout24.de/restapi/api'),
        'sandbox_url' => 'https://rest.sandbox-immobilienscout24.de/restapi/api',
        'consumer_key' => env('IMMOSCOUT_CONSUMER_KEY'),
        'consumer_secret' => env('IMMOSCOUT_CONSUMER_SECRET'),
        'access_token' => env('IMMOSCOUT_ACCESS_TOKEN'),
        'access_token_secret' => env('IMMOSCOUT_ACCESS_TOKEN_SECRET'),
    ],

    'immowelt' => [
        'ftp_host' => env('IMMOWELT_FTP_HOST'),
        'ftp_port' => env('IMMOWELT_FTP_PORT', 21),
        'ftp_username' => env('IMMOWELT_FTP_USERNAME'),
        'ftp_password' => env('IMMOWELT_FTP_PASSWORD'),
        'ftp_path' => env('IMMOWELT_FTP_PATH', '/'),
        'ftp_ssl' => env('IMMOWELT_FTP_SSL', false),
        'provider_id' => env('IMMOWELT_PROVIDER_ID', 'GEWO'),
    ],

];
