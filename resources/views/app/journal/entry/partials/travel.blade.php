<x-module>
  <x-slot:title>{{ __('Travel') }}</x-slot>
  <x-slot:emoji>✈️</x-slot>

  <div id="travel-container" class="space-y-4">
    <div class="space-y-4">
      <!-- Have you traveled today? -->
      <div class="space-y-2">
        <p>{{ __('Have you traveled today?') }}</p>
        <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
          <x-button.yes name="has_traveled" value="yes" x-target="travel-container notifications" :action="$module['has_traveled_url']" selected="{{ $entry->has_traveled_today === 'yes' }}" />
          <x-button.no name="has_traveled" value="no" x-target="travel-container notifications" :action="$module['has_traveled_url']" selected="{{ $entry->has_traveled_today === 'no' }}" />
        </div>
      </div>

      <!-- Travel mode -->
      <div class="space-y-2">
        <p>{{ __('How did you travel?') }}</p>
        <x-form x-target="travel-container notifications" :action="$module['travel_mode_url']" method="put" id="travel-mode-form">
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
  </div>
</x-module>
