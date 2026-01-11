<?php
/**
 * @var array<string, mixed> $module
 */
?>

<x-module>
  <x-slot:title>{{ __('Physical Activity') }}</x-slot>
  <x-slot:emoji>🏃‍♂️</x-slot>
  <x-slot:action>
    <div id="physical-activity-reset">
      @if ($module['display_reset'])
        <x-form x-target="physical-activity-container notifications physical-activity-reset days-listing months-listing" :action="$module['reset_url']" method="put">
          <button type="submit" class="inline cursor-pointer underline decoration-gray-300 underline-offset-4 transition-colors duration-200 hover:text-blue-600 hover:decoration-blue-400 hover:decoration-[1.15px] dark:decoration-gray-600 dark:hover:text-blue-400 dark:hover:decoration-blue-400">
            {{ __('Reset') }}
          </button>
        </x-form>
      @endif
    </div>
  </x-slot>

  <div id="physical-activity-container" x-data="{
    showActivityDetails:
      {{ $module['has_done_physical_activity'] === 'yes' ? 'true' : 'false' }},
  }">
    <div>
      <!-- Did you do physical activity? -->
      <div class="space-y-2">
        <p>{{ __('Did you do physical activity?') }}</p>
        <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
          <x-button.yes name="has_done_physical_activity" value="yes" x-target="physical-activity-container notifications physical-activity-reset days-listing months-listing" :action="$module['physical_activity_url']" selected="{{ $module['has_done_physical_activity'] === 'yes' }}" @click="showActivityDetails = true" />
          <x-button.no name="has_done_physical_activity" value="no" x-target="physical-activity-container notifications physical-activity-reset days-listing months-listing" :action="$module['physical_activity_url']" selected="{{ $module['has_done_physical_activity'] === 'no' }}" @click="showActivityDetails = false" />
        </div>
      </div>
    </div>

    <!-- Activity details (shown when has_done_physical_activity is 'yes') -->
    <div x-show="showActivityDetails" x-cloak class="mt-4 space-y-4">
      <!-- Activity type -->
      <div class="space-y-2">
        <p>{{ __('What type of activity?') }}</p>
        <div class="flex flex-wrap justify-center gap-2">
          @foreach ($module['activity_types'] as $option)
            <x-form x-target="physical-activity-container notifications physical-activity-reset days-listing months-listing" :action="$module['physical_activity_url']" method="put">
              <input type="hidden" name="activity_type" value="{{ $option['value'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} cursor-pointer rounded-lg border border-gray-200 px-3 py-2 hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
                {{ $option['label'] }}
              </button>
            </x-form>
          @endforeach
        </div>
      </div>

      <!-- Activity intensity -->
      <div class="mt-4 space-y-2">
        <p>{{ __('How intense was it?') }}</p>
        <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
          @foreach ($module['activity_intensities'] as $option)
            <div class="flex-1 border-r border-gray-200 first:overflow-hidden first:rounded-l-lg last:rounded-r-lg last:border-r-0 dark:border-gray-700">
              <x-form x-target="physical-activity-container notifications physical-activity-reset days-listing months-listing" :action="$module['physical_activity_url']" method="put">
                <input type="hidden" name="activity_intensity" value="{{ $option['value'] }}" />
                <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} flex h-full w-full cursor-pointer items-center justify-center p-2 text-center hover:bg-green-50 dark:hover:bg-green-900/40">
                  {{ $option['label'] }}
                </button>
              </x-form>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</x-module>
