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

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'evolution_api' => [
        'url' => env('EVOLUTION_API_URL', 'https://wp.chatltv.com.br'),
        'key' => env('EVOLUTION_API_KEY', 'b22f1f96-0405-4212-b1d6-40e6ef529a6e'),
        'instance' => env('EVOLUTION_API_INSTANCE', 'chatltv'),
        'timeout' => env('EVOLUTION_API_TIMEOUT', 30),
        'retry_attempts' => env('EVOLUTION_API_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('EVOLUTION_API_RETRY_DELAY', 5),
    ],

    'instance' => [
        'default' => env('INSTANCE_DEFAULT', 'default'),
        'timeout' => env('INSTANCE_TIMEOUT', 30),
        'retry_attempts' => env('INSTANCE_RETRY_ATTEMPTS', 3),
        'retry_delay' => env('INSTANCE_RETRY_DELAY', 5),
    ],

];
