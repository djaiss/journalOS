<?php
/**
 * @var array<string, mixed> $module
 */
?>

<x-module>
  <x-slot:title>{{ __('Day type') }}</x-slot>
  <x-slot:emoji>📅</x-slot>
  <x-slot:action>
    <div id="day-type-reset">
      @if ($module['display_reset'])
        <x-form x-target="day-type-container notifications day-type-reset days-listing months-listing" :action="$module['reset_url']" method="put">
          <button type="submit" class="inline cursor-pointer underline decoration-gray-300 underline-offset-4 transition-colors duration-200 hover:text-blue-600 hover:decoration-blue-400 hover:decoration-[1.15px] dark:decoration-gray-600 dark:hover:text-blue-400 dark:hover:decoration-blue-400">
            {{ __('Reset') }}
          </button>
        </x-form>
      @endif
    </div>
  </x-slot>

  <div id="day-type-container" class="space-y-4">
    <!-- Day type -->
    <div class="space-y-2">
      <p>{{ __('What type of day was it?') }}</p>
      <div class="flex flex-wrap justify-center gap-2">
        @foreach ($module['day_types'] as $option)
          <x-form x-target="day-type-container notifications day-type-reset days-listing months-listing" :action="$module['day_type_url']" method="put">
            <input type="hidden" name="day_type" value="{{ $option['value'] }}" />
            <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} cursor-pointer rounded-lg border border-gray-200 px-3 py-2 hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
              {{ $option['label'] }}
            </button>
          </x-form>
        @endforeach
      </div>
    </div>
  </div>
</x-module>
