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
                50  => 'price_1TjoaEJwVNWAqZsCZM4miz5l',
                100 => 'price_1TjoaFJwVNWAqZsCHLftDaO9',
                200 => 'price_1TjoaGJwVNWAqZsCGbHkjCJp',
                400 => 'price_1TjoaIJwVNWAqZsCm2bggkuz',
                600 => 'price_1TjoaJJwVNWAqZsCAFp9iuKd',
                800 => 'price_1TjoaKJwVNWAqZsCd4GB8nri',
                0   => 'price_1TjoaMJwVNWAqZsC0XODf3x0', // unlimited
            ],
            'links' => [
                50  => 'price_1TjoaNJwVNWAqZsCywmNDsJd',
                100 => 'price_1TjoaPJwVNWAqZsCYyGohkry',
                200 => 'price_1TjoaQJwVNWAqZsCPBR6IeTx',
                400 => 'price_1TjoaRJwVNWAqZsCm5BVgtag',
                600 => 'price_1TjoaSJwVNWAqZsCmWMlPhOS',
                800 => 'price_1TjoaTJwVNWAqZsCwig1x2VU',
                0   => 'price_1TjoaUJwVNWAqZsCUhBOlTm7', // unlimited
            ],
        ],

        // USD amounts per tier (server-side price recompute — never trust client). Key 0 = unlimited.
        'agency_tier_amounts' => [
            50 => 10, 100 => 30, 200 => 70, 400 => 150, 600 => 230, 800 => 310, 0 => 400,
        ],
        'agency_base_amount' => 49,
    ],

    // Shared secret for the read-only /api/monitoring/digest endpoint (daily cron).
    // 'slug' is the public page the daily report probes for the cloaking/API checks.
    'monitoring' => [
        'token' => env('MONITORING_TOKEN'),
        'slug'  => env('MONITORING_SLUG', 'lillylou'),
    ],

    // Telegram bot used by the daily self-monitoring report (monitoring:report).
    // The server cron sends the founder's own prod digest to his own private chat.
    'telegram' => [
        'bot_token' => env('TELEGRAM_BOT_TOKEN'),
        'chat_id'   => env('TELEGRAM_CHAT_ID'),
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
