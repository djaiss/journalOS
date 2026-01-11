<x-module>
  <x-slot:title>{{ __('Sexual activity') }}</x-slot>
  <x-slot:emoji>❤️</x-slot>
  <x-slot:action>
    <div id="sexual-activity-reset">
      @if ($module['display_reset'])
        <x-form x-target="sexual-activity-container notifications sexual-activity-reset days-listing months-listing" :action="$module['reset_url']" method="put">
          <button type="submit" class="inline cursor-pointer underline decoration-gray-300 underline-offset-4 transition-colors duration-200 hover:text-blue-600 hover:decoration-blue-400 hover:decoration-[1.15px] dark:decoration-gray-600 dark:hover:text-blue-400 dark:hover:decoration-blue-400">
            {{ __('Reset') }}
          </button>
        </x-form>
      @endif
    </div>
  </x-slot>

  <div id="sexual-activity-container" x-data="{
    showSexualActivityDetails:
      {{ $entry->moduleSexualActivity?->had_sexual_activity === 'yes' ? 'true' : 'false' }},
  }">
    <div>
      <div class="space-y-2">
        <p>{{ __('Did you have sexual activity?') }}</p>
        <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
          <x-button.yes name="had_sexual_activity" value="yes" x-target="sexual-activity-container notifications sexual-activity-reset days-listing months-listing" :action="$module['sexual_activity_url']" selected="{{ $entry->moduleSexualActivity?->had_sexual_activity === 'yes' }}" @click="showSexualActivityDetails = true" />
          <x-button.no name="had_sexual_activity" value="no" x-target="sexual-activity-container notifications sexual-activity-reset days-listing months-listing" :action="$module['sexual_activity_url']" selected="{{ $entry->moduleSexualActivity?->had_sexual_activity === 'no' }}" @click="showSexualActivityDetails = false" />
        </div>
      </div>
    </div>

    <div x-show="showSexualActivityDetails" x-cloak class="mt-4 space-y-4">
      <div class="space-y-2">
        <p>{{ __('What kind of sexual activity?') }}</p>
        <div class="flex flex-wrap justify-center gap-2">
          @foreach ($module['sexual_activity_types'] as $option)
            <x-form x-target="sexual-activity-container notifications sexual-activity-reset days-listing months-listing" :action="$module['sexual_activity_url']" method="put">
              <input type="hidden" name="sexual_activity_type" value="{{ $option['value'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} cursor-pointer rounded-lg border border-gray-200 px-3 py-2 hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
                {{ $option['label'] }}
              </button>
            </x-form>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</x-module>
