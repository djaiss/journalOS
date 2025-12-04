<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Application description
    |--------------------------------------------------------------------------
    |
    | This value is the description of the application.
    |
    */

    'description' => env('APP_DESCRIPTION', 'Memoir is the diary app for serious people.'),

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

];
