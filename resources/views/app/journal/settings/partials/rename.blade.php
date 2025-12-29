<x-box padding="p-0">
  <x-slot:title>{{ __('Rename journal') }}</x-slot>

  <x-form method="put" action="{{ route('journal.update', ['slug' => $journal->slug]) }}">
    <div class="grid grid-cols-3 items-center rounded-t-lg border-b border-gray-200 p-3 hover:bg-blue-50">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Name of the journal') }}</p>
      <div class="w-full justify-self-end">
        <x-input id="journal_name" name="journal_name" type="text" value="{{ $journal->name }}" required :error="$errors->get('journal_name')" autofocus />
      </div>
    </div>

    <div class="flex items-center justify-end p-3">
      <x-button>{{ __('Save') }}</x-button>
    </div>
  </x-form>
</x-box>
