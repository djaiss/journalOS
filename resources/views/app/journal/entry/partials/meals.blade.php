<?php

/**
 * @var array<string, mixed> $module
 */
?>

<x-module>
  <x-slot:title>{{ __('Meals') }}</x-slot>
  <x-slot:emoji>🍽️</x-slot>
  <x-slot:action>
    <div id="meals-reset">
      @if ($module['display_reset'])
        <x-form x-target="meals-container notifications meals-reset days-listing months-listing" :action="$module['reset_url']" method="put">
          <button type="submit" class="inline cursor-pointer underline decoration-gray-300 underline-offset-4 transition-colors duration-200 hover:text-blue-600 hover:decoration-blue-400 hover:decoration-[1.15px] dark:decoration-gray-600 dark:hover:text-blue-400 dark:hover:decoration-blue-400">
            {{ __('Reset') }}
          </button>
        </x-form>
      @endif
    </div>
  </x-slot>

  <div id="meals-container" class="space-y-4" x-data="{
    showMealNotes: {{ $module['has_notes'] === 'yes' ? 'true' : 'false' }},
  }">
    <div class="space-y-2">
      <p>{{ __('Meal presence') }}</p>
      <x-form x-target="meals-container notifications meals-reset days-listing months-listing" :action="$module['meals_url']" method="put" id="meal-presence-form">
        <div class="grid grid-cols-4 gap-2">
          @foreach ($module['meal_presence_options'] as $option)
            <label class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} flex cursor-pointer items-center justify-center rounded-lg border border-gray-200 p-2 text-center hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
              <input type="checkbox" name="meal_presence[]" value="{{ $option['value'] }}" {{ $option['is_selected'] ? 'checked' : '' }} onchange="document.getElementById('meal-presence-form').requestSubmit()" class="hidden" />
              <span>{{ $option['label'] }}</span>
            </label>
          @endforeach
        </div>
      </x-form>
    </div>

    <div class="space-y-2">
      <p>{{ __('Meal type') }}</p>
      <div class="grid grid-cols-2 gap-2">
        @foreach ($module['meal_type_options'] as $option)
          <x-form x-target="meals-container notifications meals-reset days-listing months-listing" :action="$module['meals_url']" method="put">
            <input type="hidden" name="meal_type" value="{{ $option['value'] }}" />
            <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} w-full rounded-lg border border-gray-200 p-2 text-center hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
              {{ $option['label'] }}
            </button>
          </x-form>
        @endforeach
      </div>
    </div>

    <div class="space-y-2">
      <p>{{ __('Social context') }}</p>
      <div class="grid grid-cols-4 gap-2">
        @foreach ($module['social_context_options'] as $option)
          <x-form x-target="meals-container notifications meals-reset days-listing months-listing" :action="$module['meals_url']" method="put">
            <input type="hidden" name="social_context" value="{{ $option['value'] }}" />
            <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} w-full rounded-lg border border-gray-200 p-2 text-center hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
              {{ $option['label'] }}
            </button>
          </x-form>
        @endforeach
      </div>
    </div>

    <div class="space-y-2">
      <p>{{ __('Notes') }}</p>
      <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
        <x-button.yes name="has_notes" value="yes" x-target="meals-container notifications meals-reset days-listing months-listing" :action="$module['meals_url']" selected="{{ $module['has_notes'] === 'yes' }}" @click="showMealNotes = true" />
        <x-button.no name="has_notes" value="no" x-target="meals-container notifications meals-reset days-listing months-listing" :action="$module['meals_url']" selected="{{ $module['has_notes'] === 'no' }}" @click="showMealNotes = false" />
      </div>
    </div>

    <div x-show="showMealNotes" x-cloak class="space-y-2">
      <x-form x-target="meals-container notifications meals-reset days-listing months-listing" :action="$module['meals_url']" method="put" class="space-y-2">
        <input type="hidden" name="has_notes" value="yes" />
        <x-textarea name="notes" placeholder="{{ __('Optional note about meals') }}" class="w-full">{{ $module['notes'] ?? '' }}</x-textarea>
        <div class="flex justify-end">
          <x-button type="submit">
            {{ __('Save note') }}
          </x-button>
        </div>
      </x-form>
    </div>
  </div>
</x-module>
