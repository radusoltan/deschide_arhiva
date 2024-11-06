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
    'elastic' => [
        'enabled' => env('ELASTIC_ENABLED', false),
        'hosts' => explode(',', env('ELASTIC_HOSTS')),
    ],
    'facebook' => [
        'client_id' => '1211926165592268',
        'client_secret' => 'a355b3d57dcf392d8527d9ee5eacea7a',
        'redirect' => 'http://localhost:8000/facebook/callback',
        'default_access_token' => 'EAAROPYyYGMwBOZC0WXzldt0JVoYXdu8QEO48bKoMaveZBWSR8SkbLRQ5VD0x9ZCtIAHA12nrEDM94kvIpQB8UkfOEJ7VxSoWDblqQZCm9vunOzFKQuRurR7quy1L4QNL0UDNVE6Iw4JG5uYonDgJuyX4GKNcfglSLoSZBn2tlHi2O4CsHqI6bZAJhzY9DosamFdhssZAK2U6pUVdyGtd8XExA74Wdo0ZBOIZD',
    ],
    'facebook_poster' => [
        'page_id' => '507699295948485',
        'access_token' => 'EAAFOZAm5DHSYBO7dCZA0sUmjPKKq2kpvMi4EVyzMiV2EefvMWXpXpwIck9OmLr8VzJZB6b68jKnBdZCUZBJKFl0NPxAOk9vIY2qd8XArhMVclOHWQSazsvsk7NaZAU4j9wstZA2doWXyuHD41a95hLR7ANy4fEBy2mcBOhfqEqTSMiZAIlGxChSuuPN2grmBa8MeviezXmRWDSNyzAZDZD',
    ]

];
