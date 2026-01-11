<?php
/**
 * @var \App\Models\Journal $journal
 */
?>

<x-box>
  <x-slot:title>
    {{ __('Delete journal') }}
  </x-slot>

  <x-slot:description>
    {{ __('This will delete the journal and all the related journal entries.') }}
  </x-slot>

  <x-form onsubmit="return confirm('Are you absolutely sure? This action cannot be undone.');" action="{{ route('journal.destroy', ['slug' => $journal->slug]) }}" method="delete">
    <x-button.danger type="submit" class="text-sm">
      {{ __('Delete journal') }}
    </x-button.danger>
  </x-form>
</x-box>
