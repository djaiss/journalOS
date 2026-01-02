<x-module>
  <x-slot:title>{{ __('Health') }}</x-slot>
  <x-slot:emoji>❤️</x-slot>
  <x-slot:action>
    <div id="health-reset">
      @if ($module['display_reset'])
        <x-form x-target="health-container notifications health-reset days-listing months-listing" :action="$module['reset_url']" method="put">
          <button type="submit" class="inline cursor-pointer underline decoration-gray-300 underline-offset-4 transition-colors duration-200 hover:text-blue-600 hover:decoration-blue-400 hover:decoration-[1.15px] dark:decoration-gray-600 dark:hover:text-blue-400 dark:hover:decoration-blue-400">
            {{ __('Reset') }}
          </button>
        </x-form>
      @endif
    </div>
  </x-slot>

  <div id="health-container">
    <div class="space-y-2">
      <p>{{ __('How did you feel today?') }}</p>
      <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
        @foreach ($module['health_options'] as $option)
          <div class="flex-1 border-r border-gray-200 first:overflow-hidden first:rounded-l-lg last:rounded-r-lg last:border-r-0 dark:border-gray-700">
            <x-form x-target="health-container notifications health-reset days-listing months-listing" :action="$module['health_url']" method="put">
              <input type="hidden" name="health" value="{{ $option['value'] }}" />
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
