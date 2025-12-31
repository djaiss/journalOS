<x-box padding="p-0" id="modules-container">
  <x-slot:title>
    {{ __('Modules') }}
  </x-slot>

  <x-slot:description>
    {{ __('Manage the modules available for this journal. Disabling a module will not delete its data. It will only hide the module from the journal.') }}
  </x-slot>

  <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="modules-form">
    <div class="grid grid-cols-3 items-center rounded-t-lg p-3 hover:bg-blue-50 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sleep module') }}</p>
      <div class="w-full justify-self-start">
        <x-toggle name="module" :checked="$journal->show_sleep_module">{{ $journal->show_sleep_module ? __('Enabled') : __('Disabled') }}</x-toggle>
      </div>
    </div>
  </x-form>
</x-box>

@if ($renderNotification ?? false)
  <div x-sync id="notifications" class="pointer-events-auto relative w-full max-w-xs transform transition duration-300 ease-in-out">
    @if ($message = Session::get('status'))
      <div x-data="{ show: true }" x-transition.duration.300ms x-show="show" x-init="setTimeout(() => (show = false), 3000)" x-transition:enter-start="translate-y-12 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave-end="scale-90 opacity-0" class="flex items-center gap-3 rounded-lg border border-green-100 bg-white p-4 text-green-700 shadow-lg dark:border-green-900/60 dark:bg-gray-900 dark:text-green-300">
        <div class="flex-shrink-0">
          <x-phosphor-check-fat class="h-5 w-5 text-green-500" />
        </div>

        <div class="min-w-0 flex-1">
          <p class="text-sm font-medium">
            {{ $message }}
          </p>
        </div>
      </div>
    @endif
  </div>
@endif
