<x-box padding="p-0" id="modules-container">
  <x-slot:title>
    {{ __('Modules') }}
  </x-slot>

  <x-slot:description>
    {{ __('Manage the modules available for this journal. Disabling a module will not delete its data. It will only hide the module from the journal.') }}
  </x-slot>

  <!-- day type module -->
  <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="day-type-module-form">
    <input type="hidden" name="module" value="day_type" />
    <div class="grid grid-cols-3 items-center rounded-t-lg border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Day type module') }}</p>
      <div class="w-full justify-self-start">
        <x-toggle name="enabled" :checked="$journal->show_day_type_module">{{ $journal->show_day_type_module ? __('Enabled') : __('Disabled') }}</x-toggle>
      </div>
    </div>
  </x-form>

  <!-- health module -->
  <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="health-module-form">
    <input type="hidden" name="module" value="health" />
    <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Health module') }}</p>
      <div class="w-full justify-self-start">
        <x-toggle name="enabled" :checked="$journal->show_health_module">{{ $journal->show_health_module ? __('Enabled') : __('Disabled') }}</x-toggle>
      </div>
    </div>
  </x-form>

  <!-- mood module -->
  <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="mood-module-form">
    <input type="hidden" name="module" value="mood" />
    <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Mood module') }}</p>
      <div class="w-full justify-self-start">
        <x-toggle name="enabled" :checked="$journal->show_mood_module">{{ $journal->show_mood_module ? __('Enabled') : __('Disabled') }}</x-toggle>
      </div>
    </div>
  </x-form>

  <!-- energy module -->
  <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="energy-module-form">
    <input type="hidden" name="module" value="energy" />
    <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Energy module') }}</p>
      <div class="w-full justify-self-start">
        <x-toggle name="enabled" :checked="$journal->show_energy_module">{{ $journal->show_energy_module ? __('Enabled') : __('Disabled') }}</x-toggle>
      </div>
    </div>
  </x-form>

  <!-- physical activity module -->
  <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="physical-activity-module-form">
    <input type="hidden" name="module" value="physical_activity" />
    <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Physical activity module') }}</p>
      <div class="w-full justify-self-start">
        <x-toggle name="enabled" :checked="$journal->show_physical_activity_module">{{ $journal->show_physical_activity_module ? __('Enabled') : __('Disabled') }}</x-toggle>
      </div>
    </div>
  </x-form>

  <!-- sexual activity module -->
  <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="sexual-activity-module-form">
    <input type="hidden" name="module" value="sexual_activity" />
    <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sexual activity module') }}</p>
      <div class="w-full justify-self-start">
        <x-toggle name="enabled" :checked="$journal->show_sexual_activity_module">{{ $journal->show_sexual_activity_module ? __('Enabled') : __('Disabled') }}</x-toggle>
      </div>
    </div>
  </x-form>

  <!-- sleep module -->
  <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="sleep-module-form">
    <input type="hidden" name="module" value="sleep" />
    <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sleep module') }}</p>
      <div class="w-full justify-self-start">
        <x-toggle name="enabled" :checked="$journal->show_sleep_module">{{ $journal->show_sleep_module ? __('Enabled') : __('Disabled') }}</x-toggle>
      </div>
    </div>
  </x-form>

  <!-- travel module -->
  <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="travel-module-form">
    <input type="hidden" name="module" value="travel" />
    <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Travel module') }}</p>
      <div class="w-full justify-self-start">
        <x-toggle name="enabled" :checked="$journal->show_travel_module">{{ $journal->show_travel_module ? __('Enabled') : __('Disabled') }}</x-toggle>
      </div>
    </div>
  </x-form>

  <!-- work module -->
  <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="work-module-form">
    <input type="hidden" name="module" value="work" />
    <div class="grid grid-cols-3 items-center rounded-b-lg p-3 hover:bg-blue-50 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Work module') }}</p>
      <div class="w-full justify-self-start">
        <x-toggle name="enabled" :checked="$journal->show_work_module">{{ $journal->show_work_module ? __('Enabled') : __('Disabled') }}</x-toggle>
      </div>
    </div>
  </x-form>
</x-box>
