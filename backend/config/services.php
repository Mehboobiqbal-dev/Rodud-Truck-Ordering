<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | Configuration for external services such as Twilio (SMS),
    | Mailgun, Postmark, AWS SES, etc.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Twilio (SMS Notifications)
    |--------------------------------------------------------------------------
    */
    'twilio' => [
        'auth_sid'   => env('TWILIO_AUTH_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'phone_number' => env('TWILIO_PHONE_NUMBER'),
    ],
];
