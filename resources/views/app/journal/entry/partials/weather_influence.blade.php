<?php

/**
 * @var array<string, mixed> $module
 */
?>

<x-module>
  <x-slot:title>{{ __('Weather influence') }}</x-slot>
  <x-slot:emoji>🌬️</x-slot>
  <x-slot:action>
    <div id="weather-influence-reset">
      @if ($module['display_reset'])
        <x-form x-target="weather-influence-container notifications weather-influence-reset days-listing months-listing" :action="$module['reset_url']" method="put">
          <button type="submit" class="inline cursor-pointer underline decoration-gray-300 underline-offset-4 transition-colors duration-200 hover:text-blue-600 hover:decoration-blue-400 hover:decoration-[1.15px] dark:decoration-gray-600 dark:hover:text-blue-400 dark:hover:decoration-blue-400">
            {{ __('Reset') }}
          </button>
        </x-form>
      @endif
    </div>
  </x-slot>

  <div id="weather-influence-container" class="space-y-4">
    <div class="space-y-2">
      <p>{{ __('Did the weather affect my mood today?') }}</p>
      <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
        @foreach ($module['mood_effect_options'] as $option)
          <div class="flex-1 border-r border-gray-200 first:overflow-hidden first:rounded-l-lg last:rounded-r-lg last:border-r-0 dark:border-gray-700">
            <x-form x-target="weather-influence-container notifications weather-influence-reset days-listing months-listing" :action="$module['weather_influence_url']" method="put">
              <input type="hidden" name="mood_effect" value="{{ $option['value'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} flex h-full w-full cursor-pointer items-center justify-center p-2 text-center hover:bg-green-50 dark:hover:bg-green-900/40">
                {{ $option['label'] }}
              </button>
            </x-form>
          </div>
        @endforeach
      </div>
    </div>

    <div class="space-y-2">
      <p>{{ __('Did it affect my energy?') }}</p>
      <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
        @foreach ($module['energy_effect_options'] as $option)
          <div class="flex-1 border-r border-gray-200 first:overflow-hidden first:rounded-l-lg last:rounded-r-lg last:border-r-0 dark:border-gray-700">
            <x-form x-target="weather-influence-container notifications weather-influence-reset days-listing months-listing" :action="$module['weather_influence_url']" method="put">
              <input type="hidden" name="energy_effect" value="{{ $option['value'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} flex h-full w-full cursor-pointer items-center justify-center p-2 text-center hover:bg-green-50 dark:hover:bg-green-900/40">
                {{ $option['label'] }}
              </button>
            </x-form>
          </div>
        @endforeach
      </div>
    </div>

    <div class="space-y-2">
      <p>{{ __('Did it influence my plans?') }}</p>
      <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
        @foreach ($module['plans_influence_options'] as $option)
          <div class="flex-1 border-r border-gray-200 first:overflow-hidden first:rounded-l-lg last:rounded-r-lg last:border-r-0 dark:border-gray-700">
            <x-form x-target="weather-influence-container notifications weather-influence-reset days-listing months-listing" :action="$module['weather_influence_url']" method="put">
              <input type="hidden" name="plans_influence" value="{{ $option['value'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} flex h-full w-full cursor-pointer items-center justify-center p-2 text-center hover:bg-green-50 dark:hover:bg-green-900/40">
                {{ $option['label'] }}
              </button>
            </x-form>
          </div>
        @endforeach
      </div>
    </div>

    <div class="space-y-2">
      <p>{{ __('Was I outside much today?') }}</p>
      <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
        @foreach ($module['outside_time_options'] as $option)
          <div class="flex-1 border-r border-gray-200 first:overflow-hidden first:rounded-l-lg last:rounded-r-lg last:border-r-0 dark:border-gray-700">
            <x-form x-target="weather-influence-container notifications weather-influence-reset days-listing months-listing" :action="$module['weather_influence_url']" method="put">
              <input type="hidden" name="outside_time" value="{{ $option['value'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} flex h-full w-full cursor-pointer items-center justify-center p-2 text-center hover:bg-green-50 dark:hover:bg-green-900/40">
                {{ $option['label'] }}
              </button>
            </x-form>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</x-module>
