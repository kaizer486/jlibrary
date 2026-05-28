<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Payment Gateways Configuration
    |--------------------------------------------------------------------------
    */
    
    'currencies' => [
        'default' => 'TZS',
        'symbol' => 'TSh',
        'code' => 'TZS'
    ],
    
    'gateways' => [
        'mpesa' => [
            'name' => 'M-Pesa',
            'enabled' => env('MPESA_ENABLED', true),
            'environment' => env('MPESA_ENVIRONMENT', 'sandbox'),
            'consumer_key' => env('MPESA_CONSUMER_KEY'),
            'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
            'passkey' => env('MPESA_PASSKEY'),
            'shortcode' => env('MPESA_SHORTCODE', '174379'),
            'callback_url' => env('MPESA_CALLBACK_URL', '/api/payments/mpesa/callback'),
            'min_amount' => 10,
            'max_amount' => 500000,
            'icon' => 'ti-device-mobile',
            'color' => 'green'
        ],
        
        'tigopesa' => [
            'name' => 'TigoPesa',
            'enabled' => env('TIGOPESA_ENABLED', true),
            'environment' => env('TIGOPESA_ENVIRONMENT', 'sandbox'),
            'api_key' => env('TIGOPESA_API_KEY'),
            'api_secret' => env('TIGOPESA_API_SECRET'),
            'shortcode' => env('TIGOPESA_SHORTCODE'),
            'callback_url' => env('TIGOPESA_CALLBACK_URL', '/api/payments/tigopesa/callback'),
            'min_amount' => 10,
            'max_amount' => 500000,
            'icon' => 'ti-device-mobile',
            'color' => 'blue'
        ],
        
        'halopesa' => [
            'name' => 'HaloPesa',
            'enabled' => env('HALOPESA_ENABLED', true),
            'environment' => env('HALOPESA_ENVIRONMENT', 'sandbox'),
            'api_key' => env('HALOPESA_API_KEY'),
            'api_secret' => env('HALOPESA_API_SECRET'),
            'shortcode' => env('HALOPESA_SHORTCODE'),
            'callback_url' => env('HALOPESA_CALLBACK_URL', '/api/payments/halopesa/callback'),
            'min_amount' => 10,
            'max_amount' => 500000,
            'icon' => 'ti-device-mobile',
            'color' => 'red'
        ],
        
        'card' => [
            'name' => 'Credit / Debit Card',
            'enabled' => env('STRIPE_ENABLED', true),
            'driver' => 'stripe',
            'public_key' => env('STRIPE_KEY'),
            'secret_key' => env('STRIPE_SECRET'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
            'min_amount' => 50,
            'max_amount' => 1000000,
            'icon' => 'ti-credit-card',
            'color' => 'purple'
        ],
        
        'bank' => [
            'name' => 'Bank Transfer',
            'enabled' => env('BANK_ENABLED', true),
            'requires_approval' => true,
            'min_amount' => 1000,
            'max_amount' => 10000000,
            'icon' => 'ti-building-bank',
            'color' => 'gray',
            'bank_details' => [
                'bank_name' => env('BANK_NAME', 'CRDB Bank'),
                'account_name' => env('BANK_ACCOUNT_NAME', 'JLIBRARY LTD'),
                'account_number' => env('BANK_ACCOUNT_NUMBER'),
                'swift_code' => env('BANK_SWIFT_CODE'),
                'branch' => env('BANK_BRANCH', 'Head Office')
            ]
        ]
    ],
    
    'commission' => [
        'marketplace_seller' => 80, // 80% to seller
        'platform_fee' => 20, // 20% platform fee
    ]
];