<?php
/**
 * @var array<string, mixed> $module
 */
?>

<x-module>
  <x-slot:title>{{ __('Social events') }}</x-slot>
  <x-slot:emoji>🤝</x-slot>
  <x-slot:action>
    <div id="social-events-reset">
      @if ($module['display_reset'])
        <x-form x-target="social-events-container notifications social-events-reset days-listing months-listing" :action="$module['reset_url']" method="put">
          <button type="submit" class="inline cursor-pointer underline decoration-gray-300 underline-offset-4 transition-colors duration-200 hover:text-blue-600 hover:decoration-blue-400 hover:decoration-[1.15px] dark:decoration-gray-600 dark:hover:text-blue-400 dark:hover:decoration-blue-400">
            {{ __('Reset') }}
          </button>
        </x-form>
      @endif
    </div>
  </x-slot>

  <div id="social-events-container" class="space-y-4">
    <div class="space-y-2">
      <p>{{ __('What kind of social event did you have?') }}</p>
      <div class="grid grid-cols-3 gap-2">
        @foreach ($module['event_type_options'] as $option)
          <x-form x-target="social-events-container notifications social-events-reset days-listing months-listing" :action="$module['social_events_url']" method="put">
            <input type="hidden" name="event_type" value="{{ $option['value'] }}" />
            <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} w-full rounded-lg border border-gray-200 p-2 text-center hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
              {{ $option['label'] }}
            </button>
          </x-form>
        @endforeach
      </div>
    </div>

    @if ($module['has_event_type'])
      <div class="space-y-2">
        <p>{{ __('How did it feel?') }}</p>
        <div class="grid grid-cols-3 gap-2">
          @foreach ($module['tone_options'] as $option)
            <x-form x-target="social-events-container notifications social-events-reset days-listing months-listing" :action="$module['social_events_url']" method="put">
              <input type="hidden" name="event_type" value="{{ $module['event_type'] }}" />
              <input type="hidden" name="tone" value="{{ $option['value'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} w-full rounded-lg border border-gray-200 p-2 text-center hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
                {{ $option['label'] }}
              </button>
            </x-form>
          @endforeach
        </div>
      </div>
    @endif

    @if ($module['has_tone'])
      <div class="space-y-2">
        <p>{{ __('How long did it last?') }}</p>
        <div class="grid grid-cols-3 gap-2">
          @foreach ($module['duration_options'] as $option)
            <x-form x-target="social-events-container notifications social-events-reset days-listing months-listing" :action="$module['social_events_url']" method="put">
              <input type="hidden" name="event_type" value="{{ $module['event_type'] }}" />
              <input type="hidden" name="tone" value="{{ $module['tone'] }}" />
              <input type="hidden" name="duration" value="{{ $option['value'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} w-full rounded-lg border border-gray-200 p-2 text-center hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
                {{ $option['label'] }}
              </button>
            </x-form>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</x-module>
