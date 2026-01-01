<x-module>
  <x-slot:title>{{ __('Day type') }}</x-slot>
  <x-slot:emoji>📅</x-slot>
  <x-slot:action>
    <div id="reset">
      @if ($module['display_reset'])
        <x-form x-target="sleep-container notifications reset" :action="$module['reset_url']" method="put">
          <button type="submit" class="inline cursor-pointer underline decoration-gray-300 underline-offset-4 transition-colors duration-200 hover:text-blue-600 hover:decoration-blue-400 hover:decoration-[1.15px] dark:decoration-gray-600 dark:hover:text-blue-400 dark:hover:decoration-blue-400">
            {{ __('Reset') }}
          </button>
        </x-form>
      @endif
    </div>
  </x-slot>

  <div id="sleep-container" class="space-y-4">
    <!-- Day type -->
    <div class="space-y-2">
      <p>{{ __('What type of day was it?') }}</p>
      <x-form x-target="travel-container notifications travel-reset" :action="$module['travel_mode_url']" method="put" id="travel-mode-form">
        <div class="grid grid-cols-4 gap-2">
          @foreach ($module['travel_modes'] as $option)
            <label class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} flex cursor-pointer items-center justify-center rounded-lg border border-gray-200 p-2 text-center hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
              <input type="checkbox" name="travel_modes[]" value="{{ $option['value'] }}" {{ $option['is_selected'] ? 'checked' : '' }} onchange="document.getElementById('travel-mode-form').requestSubmit()" class="hidden" />
              <span>{{ $option['label'] }}</span>
            </label>
          @endforeach
        </div>
      </x-form>
    </div>
  </div>
</x-module>
