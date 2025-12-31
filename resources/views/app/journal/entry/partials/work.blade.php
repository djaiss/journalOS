<x-module>
  <x-slot:title>{{ __('Work') }}</x-slot>
  <x-slot:emoji>💼</x-slot>
  <x-slot:action>
    <div id="reset">
      @if ($module['display_reset'])
        <x-form x-target="work-container notifications reset" :action="$module['reset_url']" method="put">
          <button type="submit" class="inline cursor-pointer underline decoration-gray-300 underline-offset-4 transition-colors duration-200 hover:text-blue-600 hover:decoration-blue-400 hover:decoration-[1.15px] dark:decoration-gray-600 dark:hover:text-blue-400 dark:hover:decoration-blue-400">
            {{ __('Reset') }}
          </button>
        </x-form>
      @endif
    </div>
  </x-slot>

  <div id="work-container" class="space-y-4">
    <div class="space-y-2">
      <!-- Have you worked today? -->
      <p>{{ __('Have you worked today?') }}</p>
      <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
        <x-button.yes name="worked" value="yes" x-target="work-container notifications reset" :action="$module['has_worked_url']" selected="{{ $entry->worked === 'yes' }}" />
        <x-button.no name="worked" value="no" x-target="work-container notifications reset" :action="$module['has_worked_url']" selected="{{ $entry->worked === 'no' }}" />
      </div>
    </div>
  </div>
</x-module>
