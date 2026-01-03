<x-module>
  <x-slot:title>{{ __('Kids today') }}</x-slot>
  <x-slot:emoji>🧒</x-slot>
  <x-slot:action>
    <div id="kids-reset">
      @if ($module['display_reset'])
        <x-form x-target="kids-container notifications kids-reset days-listing months-listing" :action="$module['reset_url']" method="put">
          <button type="submit" class="inline cursor-pointer underline decoration-gray-300 underline-offset-4 transition-colors duration-200 hover:text-blue-600 hover:decoration-blue-400 hover:decoration-[1.15px] dark:decoration-gray-600 dark:hover:text-blue-400 dark:hover:decoration-blue-400">
            {{ __('Reset') }}
          </button>
        </x-form>
      @endif
    </div>
  </x-slot>

  <div id="kids-container" class="space-y-4">
    <div class="space-y-2">
      <p>{{ __('Did you have the kids today?') }}</p>
      <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
        <x-button.yes name="had_kids_today" value="yes" x-target="kids-container notifications kids-reset days-listing months-listing" :action="$module['had_kids_today_url']" selected="{{ $entry->had_kids_today === 'yes' }}" />
        <x-button.no name="had_kids_today" value="no" x-target="kids-container notifications kids-reset days-listing months-listing" :action="$module['had_kids_today_url']" selected="{{ $entry->had_kids_today === 'no' }}" />
      </div>
    </div>
  </div>
</x-module>
