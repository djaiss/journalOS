<x-box padding="p-0" id="modules-container">
  <x-slot:title>
    {{ __('Modules') }}
  </x-slot>

  <x-slot:description>
    {{ __('Manage the modules available for this journal. Disabling a module will not delete its data. It will only hide the module from the journal.') }}
  </x-slot>

  <!-- sleep module -->
  <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="sleep-module-form">
    <input type="hidden" name="module" value="sleep" />
    <div class="grid grid-cols-3 items-center rounded-t-lg border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sleep module') }}</p>
      <div class="w-full justify-self-start">
        <x-toggle name="enabled" :checked="$journal->show_sleep_module">{{ $journal->show_sleep_module ? __('Enabled') : __('Disabled') }}</x-toggle>
      </div>
    </div>
  </x-form>

  <!-- work module -->
  <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="work-module-form">
    <input type="hidden" name="module" value="work" />
    <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Work module') }}</p>
      <div class="w-full justify-self-start">
        <x-toggle name="enabled" :checked="$journal->show_work_module">{{ $journal->show_work_module ? __('Enabled') : __('Disabled') }}</x-toggle>
      </div>
    </div>
  </x-form>

  <!-- travel module -->
  <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="travel-module-form">
    <input type="hidden" name="module" value="travel" />
    <div class="grid grid-cols-3 items-center rounded-b-lg p-3 hover:bg-blue-50 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Travel module') }}</p>
      <div class="w-full justify-self-start">
        <x-toggle name="enabled" :checked="$journal->show_travel_module">{{ $journal->show_travel_module ? __('Enabled') : __('Disabled') }}</x-toggle>
      </div>
    </div>
  </x-form>
</x-box>
