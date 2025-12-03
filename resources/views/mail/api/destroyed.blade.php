<x-mail::message>
  # API key removed

  A personal API key with the label {{ $label }} has been removed on {{ config('app.name') }}.

  <x-mail::panel>If you did not authorize this action, please contact support.</x-mail::panel>

  Thanks,<br>
  {{ config('app.name') }}
</x-mail::message>
