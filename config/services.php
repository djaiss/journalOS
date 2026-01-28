<?php

declare(strict_types=1);

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

    'cloudflare' => [
        'zone_id' => env('CLOUDFLARE_ZONE_ID'),
        'api_token' => env('CLOUDFLARE_API_TOKEN'),
        'purge_secret' => env('CLOUDFLARE_PURGE_SECRET'),
        'purge_chunk' => env('CLOUDFLARE_PURGE_CHUNK', 30),
    ],

    'journalos' => [
        /*
        |--------------------------------------------------------------------------
        | Application description
        |--------------------------------------------------------------------------
        |
        | This value is the description of the application.
        |
        */

        'description' => env('APP_DESCRIPTION', 'JournalOS is an online bullet journal designed to help you document your life.'),

        /*
        |--------------------------------------------------------------------------
        | Show the marketing site
        |--------------------------------------------------------------------------
        |
        | This value enables the marketing site to be shown. If you
        | self host the application, you probably want to disable this since
        | you don't need to show the marketing site.
        |
        */

        'show_marketing_site' => env('SHOW_MARKETING_SITE', true),

        /*
        |--------------------------------------------------------------------------
        | Enable the paid version
        |--------------------------------------------------------------------------
        |
        | This value enables the paid version of the application. If you
        | self host the application, you probably want to disable this since
        | you will not have a way to purchase a lifetime access.
        |
        */

        'enable_paid_version' => env('ENABLE_PAID_VERSION', false),

        /*
        |--------------------------------------------------------------------------
        | Supported locales
        |--------------------------------------------------------------------------
        |
        | This value enables the supported locales of the application.
        |
        */

        'supported_locales' => ['en', 'fr'],

        /*
        |--------------------------------------------------------------------------
        | Use Resend to send transactional emails
        |--------------------------------------------------------------------------
        |
        | This value enables the use of Resend to send transactional emails.
        | If you self host the application, you probably want to disable this
        | since you don't need to send transactional emails.
        |
        */

        'use_resend' => env('USE_RESEND', false),

        /*
        |--------------------------------------------------------------------------
        | Email that receives account deletion notifications
        |--------------------------------------------------------------------------
        |
        | This email is used to receive notifications when an account is deleted.
        |
        */

        'account_deletion_notification_email' => env('ACCOUNT_DELETION_NOTIFICATION_EMAIL', 'hello@example.com'),
    ],

    'turnstile' => [
        'key' => env('TURNSTILE_SITE_KEY'),
        'secret' => env('TURNSTILE_SECRET_KEY'),
    ],
];
