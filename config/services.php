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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Pay Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para Google Pay como pasarela de pago.
    | 
    | - api_endpoint: URL base de la API de Google Pay
    | - merchant_id: ID del comercio en Google Pay
    | - secret_key: Clave secreta para autenticación
    | - sandbox: Modo de pruebas (true) o producción (false)
    | - simulate_success: Simular pagos exitosos en modo sandbox
    |
    */
    'google_pay' => [
        'api_endpoint' => env('GOOGLE_PAY_API_ENDPOINT', 'https://api.googlepay.com/v1'),
        'merchant_id' => env('GOOGLE_PAY_MERCHANT_ID', 'test_merchant_12345'),
        'secret_key' => env('GOOGLE_PAY_SECRET_KEY', 'test_secret_key_abcdefg'),
        'sandbox' => env('GOOGLE_PAY_SANDBOX', true),
        'simulate_success' => env('GOOGLE_PAY_SIMULATE_SUCCESS', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Mercado Pago Configuration
    |--------------------------------------------------------------------------
    |
    | Configuración para Mercado Pago (implementación futura).
    |
    */
    'mercadopago' => [
        'access_token' => env('MERCADOPAGO_ACCESS_TOKEN'),
        'public_key' => env('MERCADOPAGO_PUBLIC_KEY'),
    ],

];
