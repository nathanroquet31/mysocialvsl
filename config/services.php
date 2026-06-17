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

    'stripe' => [
        'key'               => env('STRIPE_KEY'),
        'secret'            => env('STRIPE_SECRET'),
        'webhook_secret'    => env('STRIPE_WEBHOOK_SECRET'),
        'price_pro'         => env('STRIPE_PRICE_PRO'),
        'price_agency'      => env('STRIPE_PRICE_AGENCY'),       // base Agency $49 — includes 25 pages + 25 links
        'price_extra_pages' => env('STRIPE_PRICE_EXTRA_PAGES'),
        'price_extra_links' => env('STRIPE_PRICE_EXTRA_LINKS'),

        // Agency capacity tiers. Base ($49) already includes 25 pages + 25 links,
        // so only tiers above 25 add an extra line item. Key 0 = unlimited (∞).
        // Price IDs are not secrets (they are exposed client-side at checkout).
        'agency_tiers' => [
            'pages' => [
                50  => 'price_1TijAnJwVNWAqZsCakwQ2oma',
                100 => 'price_1TijAoJwVNWAqZsCJaIYPhZf',
                200 => 'price_1TijAoJwVNWAqZsCth8uHijC',
                400 => 'price_1TijApJwVNWAqZsCaBWdtatQ',
                600 => 'price_1TijAqJwVNWAqZsCqp64AIWo',
                800 => 'price_1TijArJwVNWAqZsCQ3jmRwKY',
                0   => 'price_1TijAsJwVNWAqZsCLM4FbxlK', // unlimited
            ],
            'links' => [
                50  => 'price_1TijAsJwVNWAqZsCu0F4CLbj',
                100 => 'price_1TijAtJwVNWAqZsCWCR1k97Q',
                200 => 'price_1TijAuJwVNWAqZsCmNXN4oZH',
                400 => 'price_1TijAvJwVNWAqZsC3rhaqPHm',
                600 => 'price_1TijAwJwVNWAqZsCaodlRypM',
                800 => 'price_1TijAwJwVNWAqZsCvj6eY4nW',
                0   => 'price_1TijAxJwVNWAqZsCLrbmBCjN', // unlimited
            ],
        ],

        // USD amounts per tier (server-side price recompute — never trust client). Key 0 = unlimited.
        'agency_tier_amounts' => [
            50 => 10, 100 => 30, 200 => 70, 400 => 150, 600 => 230, 800 => 310, 0 => 400,
        ],
        'agency_base_amount' => 49,
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    // Instagram Graph API — pour le module Social Analytics
    'instagram' => [
        'client_id'     => env('INSTAGRAM_CLIENT_ID'),
        'client_secret' => env('INSTAGRAM_CLIENT_SECRET'),
        'redirect'      => env('INSTAGRAM_REDIRECT_URI'),
    ],

];
