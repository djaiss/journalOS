<?php

/**
 * @var \App\Models\Journal $journal
 * @var \Illuminate\Support\ViewErrorBag $errors
 */
?>

<x-box padding="p-0">
  <x-slot:title>{{ __('Editing capabilities') }}</x-slot>
  <x-slot:description>
    <p>{{ __('By default, journal entries older than 7 days can\'t be edited.') }}</p>
    <p>{{ __('You can disable this behaviour to edit older entries. This is cheating a bit, though.') }}</p>
  </x-slot>

  <x-form method="put" x-target="edit-past-form notifications" x-target.back="edit-past-form" id="edit-past-form" :action="route('journal.settings.edit-past.update', ['slug' => $journal->slug])">
    <!-- can edit past entries -->
    <div class="grid grid-cols-3 items-center rounded-t-lg border-b border-gray-200 p-3 last:rounded-b-lg hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Can edit past entries') }}</p>
      <div class="col-span-1 w-full justify-self-end">
        <x-select id="can_edit_past" name="can_edit_past" :options="[
          '1' => __('Yes'),
          '0' => __('No')
        ]" selected="{{ old('can_edit_past', $journal->can_edit_past ? '1' : '0') }}" required :error="$errors->get('can_edit_past')" />
      </div>
    </div>

    <!-- actions -->
    <div class="flex items-center justify-end p-3">
      <x-button>{{ __('Save') }}</x-button>
    </div>
  </x-form>
</x-box>
