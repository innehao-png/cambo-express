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

    'bakong' => [
        'base_url' => env('BAKONG_BASE_URL'),
        'account_name' => env('BAKONG_ACCOUNT_NAME'),
        'account_username' => env('BAKONG_ACCOUNT_USERNAME'),
        'phone' => env('BAKONG_PHONE_NUMBER'),
        'api_token' => env('BAKONG_TOKEN'),
    ],
    'aba' => [
        'api_url' => env('PAYWAY_API_URL'),
        'base_url' => env('ABA_BASE_URL'),
        'merchant_id' => env('PAYWAY_MERCHANT_ID'),
        'public_key' => env('PAYWAY_PUBLIC_KEY'),
        'rsa_public_key' => env('PAYWAY_RSA_PUBLIC_KEY'),
        'rsa_private_key' => env('PAYWAY_RSA_PRIVATE_KEY'),
    ]
];
