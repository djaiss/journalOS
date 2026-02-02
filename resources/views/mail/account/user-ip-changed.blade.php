<?php

/**
 * @var string $email
 * @var string $ip
 */
?>

<x-mail::message>
Hi,

Your {{ config('app.name') }} account {{ $email }} was recently signed-in from a new location, device or browser.

<x-mail::panel>
Time: {{ now()->toDayDateTimeString() }}

IP Address: {{ $ip }}
</x-mail::panel>

We are sending you this email because we were unable to determine if you have signed-in from this location or browser before. This may be because you are traveling, using a VPN or Private Relay, a new or updated browser, or another person is using your account.<br>

Thanks,<br>
{{ config('app.name') }}

</x-mail::message>
