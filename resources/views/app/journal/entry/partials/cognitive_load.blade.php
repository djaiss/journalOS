<?php
/**
 * @var array<string, mixed> $module
 */
?>

<x-module>
  <x-slot:title>{{ __('Cognitive load') }}</x-slot>
  <x-slot:emoji>🧠</x-slot>
  <x-slot:action>
    <div id="cognitive-load-reset">
      @if ($module['display_reset'])
        <x-form x-target="cognitive-load-container notifications cognitive-load-reset days-listing months-listing" :action="$module['reset_url']" method="put">
          <button type="submit" class="inline cursor-pointer underline decoration-gray-300 underline-offset-4 transition-colors duration-200 hover:text-blue-600 hover:decoration-blue-400 hover:decoration-[1.15px] dark:decoration-gray-600 dark:hover:text-blue-400 dark:hover:decoration-blue-400">
            {{ __('Reset') }}
          </button>
        </x-form>
      @endif
    </div>
  </x-slot>

  <div id="cognitive-load-container" class="space-y-4">
    <div class="space-y-2">
      <p>{{ __('How heavy was your cognitive load today?') }}</p>
      <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
        @foreach ($module['cognitive_load_options'] as $option)
          <div class="flex-1 border-r border-gray-200 first:overflow-hidden first:rounded-l-lg last:rounded-r-lg last:border-r-0 dark:border-gray-700">
            <x-form x-target="cognitive-load-container notifications cognitive-load-reset days-listing months-listing" :action="$module['cognitive_load_url']" method="put" class="h-full">
              <input type="hidden" name="cognitive_load" value="{{ $option['value'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} flex h-full w-full cursor-pointer items-center justify-center p-2 text-center hover:bg-green-50 dark:hover:bg-green-900/40">
                {{ $option['label'] }}
              </button>
            </x-form>
          </div>
        @endforeach
      </div>
    </div>

    @if ($module['has_cognitive_load'])
      <div class="space-y-2">
        <p>{{ __('Primary source') }}</p>
        <div class="grid grid-cols-3 gap-2">
          @foreach ($module['primary_source_options'] as $option)
            <x-form x-target="cognitive-load-container notifications cognitive-load-reset days-listing months-listing" :action="$module['cognitive_load_url']" method="put">
              <input type="hidden" name="cognitive_load" value="{{ $module['cognitive_load'] }}" />
              <input type="hidden" name="primary_source" value="{{ $option['value'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} w-full rounded-lg border border-gray-200 p-2 text-center hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
                {{ $option['label'] }}
              </button>
            </x-form>
          @endforeach
        </div>
      </div>

      <div class="space-y-2">
        <p>{{ __('Load quality') }}</p>
        <div class="grid grid-cols-3 gap-2">
          @foreach ($module['load_quality_options'] as $option)
            <x-form x-target="cognitive-load-container notifications cognitive-load-reset days-listing months-listing" :action="$module['cognitive_load_url']" method="put">
              <input type="hidden" name="cognitive_load" value="{{ $module['cognitive_load'] }}" />
              <input type="hidden" name="load_quality" value="{{ $option['value'] }}" />
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
