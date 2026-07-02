<?php

return [

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

    'sensor' => [
        'token' => env('SENSOR_API_TOKEN'),
    ],

    'sms' => [
        'providers' => array_values(array_filter(array_map(
            'trim',
            explode(',', (string) env('SMS_PROVIDERS', 'log'))
        ))),

        'delay_step_minutes' => (int) env('SMS_DELAY_STEP_MINUTES', 10),

        'circuit_breaker' => [
            'threshold' => (int) env('SMS_CB_THRESHOLD', 5),
            'cooldown'  => (int) env('SMS_CB_COOLDOWN_SECONDS', 120),
        ],

        'twilio' => [
            'sid'   => env('TWILIO_SID'),
            'token' => env('TWILIO_TOKEN'),
            'from'  => env('TWILIO_FROM'),
        ],

        'vonage' => [
            'key'    => env('VONAGE_KEY'),
            'secret' => env('VONAGE_SECRET'),
            'from'   => env('VONAGE_FROM', 'Train'),
        ],
    ],

];
