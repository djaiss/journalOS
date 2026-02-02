<?php

/**
 * @var string $email
 * @var string $ip
 */
?>

Hi,

Your {{ config('app.name') }} account {{ $email }} was recently signed-in from a new location, device or browser.

Time: {{ now()->toDayDateTimeString() }}

IP Address: {{ $ip }}

We are sending you this email because we were unable to determine if you have signed-in from this location or browser before. This may be because you are traveling, using a VPN or Private Relay, a new or updated browser, or another person is using your account.

Thanks,
{{ config('app.name') }}
