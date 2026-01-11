<?php
/**
 * @var int $age
 */
?>

<x-mail::message>
Account deleted

An account has been automatically deleted because of inactivity.

<x-mail::panel>
Was in the system for: {{ $age }}
</x-mail::panel>
</x-mail::message>
