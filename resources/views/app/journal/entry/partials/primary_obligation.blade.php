<?php
/**
 * @var array<string, mixed> $module
 */
?>

<x-module>
  <x-slot:title>{{ __('Primary obligation') }}</x-slot>
  <x-slot:emoji>🎯</x-slot>
  <x-slot:action>
    <div id="primary-obligation-reset">
      @if ($module['display_reset'])
        <x-form x-target="primary-obligation-container notifications primary-obligation-reset days-listing months-listing" :action="$module['reset_url']" method="put">
          <button type="submit" class="inline cursor-pointer underline decoration-gray-300 underline-offset-4 transition-colors duration-200 hover:text-blue-600 hover:decoration-blue-400 hover:decoration-[1.15px] dark:decoration-gray-600 dark:hover:text-blue-400 dark:hover:decoration-blue-400">
            {{ __('Reset') }}
          </button>
        </x-form>
      @endif
    </div>
  </x-slot>

  <div id="primary-obligation-container">
    <div class="space-y-2">
      <p>{{ __('What demanded most of your attention today?') }}</p>
      <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('It captures priority, not time spent and not emotion.') }}</p>
      <div class="grid grid-cols-3 gap-2">
        @foreach ($module['primary_obligation_options'] as $option)
          <x-form x-target="primary-obligation-container notifications primary-obligation-reset days-listing months-listing" :action="$module['primary_obligation_url']" method="put">
            <input type="hidden" name="primary_obligation" value="{{ $option['value'] }}" />
            <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} w-full rounded-lg border border-gray-200 p-2 text-center hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
              {{ $option['label'] }}
            </button>
          </x-form>
        @endforeach
      </div>
    </div>
  </div>
</x-module>
