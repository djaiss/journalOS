<?php

/**
 * @var string $activeSince
 * @var string $reason
 */
?>

<x-mail::message>
Account deleted

An account has been deleted.

<x-mail::panel>
Reason: {{ $reason }}

Active since: {{ $activeSince }}
</x-mail::panel>
</x-mail::message>
