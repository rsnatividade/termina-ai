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
        'key' => env('EVOLUTION_API_KEY', '504326E0B724-46C8-9C93-B41E080EB2A8'),
        'instance' => env('EVOLUTION_API_INSTANCE', 'chatltvcodecon'),
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

    'openai' => [
        'key' => env('OPENAI_API_KEY'),
        'url' => env('OPENAI_API_URL', 'https://api.openai.com/v1/chat/completions'),
        'model' => env('OPENAI_MODEL', 'gpt-4'),
        'max_tokens' => env('OPENAI_MAX_TOKENS', 1000),
        'temperature' => env('OPENAI_TEMPERATURE', 0.7),
    ],

];
