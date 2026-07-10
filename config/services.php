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
    'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI'),
],

'github' => [
    'client_id' => env('GITHUB_CLIENT_ID'),
    'client_secret' => env('GITHUB_CLIENT_SECRET'),
    'redirect' => env('GITHUB_REDIRECT_URI'),
],

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

     'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        'base_url' => 'https://generativelanguage.googleapis.com/v1',
        'timeout' => 60,
        'retry_times' => 3,
    ],
    'pesapal' => [
    'consumer_key' => env('PESAPAL_CONSUMER_KEY'),
    'consumer_secret' => env('PESAPAL_CONSUMER_SECRET'),
    'environment' => env('PESAPAL_ENVIRONMENT', 'sandbox'),
],

'mpesa' => [
    'consumer_key' => env('MPESA_CONSUMER_KEY'),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
    'passkey' => env('MPESA_PASSKEY'),
    'shortcode' => env('MPESA_SHORTCODE'),
    'callback_url' => env('MPESA_CALLBACK_URL'),
    'environment' => env('MPESA_ENVIRONMENT', 'sandbox'),
],
 'tigopesa' => [
        'api_key' => env('TIGOPESA_API_KEY'),
        'api_secret' => env('TIGOPESA_API_SECRET'),
        'callback_url' => env('TIGOPESA_CALLBACK_URL'),
        'environment' => env('TIGOPESA_ENVIRONMENT', 'sandbox'),
    ],
    
    'halopesa' => [
        'api_key' => env('HALOPESA_API_KEY'),
        'api_secret' => env('HALOPESA_API_SECRET'),
        'callback_url' => env('HALOPESA_CALLBACK_URL'),
        'environment' => env('HALOPESA_ENVIRONMENT', 'sandbox'),
    ],

];

