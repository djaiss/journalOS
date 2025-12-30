<x-box padding="p-0">
  <x-slot:title>
    {{ __('Modules') }}
  </x-slot:title>

  <x-slot:description>
    {{ __('Manage the modules available for this journal. Disabling a module will not delete its data. It will only hide the module from the journal.') }}
  </x-slot:description>

  <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}">
    <div class="grid grid-cols-3 items-center rounded-t-lg p-3 hover:bg-blue-50">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sleep module') }}</p>
      <div class="w-full justify-self-start">
        <x-toggle name="sleep"></x-toggle>
      </div>
    </div>
  </x-form>
</x-box>
