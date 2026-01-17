<?php
/**
 * @var array<string, mixed> $module
 */
?>

<x-module>
  <x-slot:title>{{ __('Meals') }}</x-slot>
  <x-slot:emoji>🍽️</x-slot>
  <x-slot:action>
    <div id="meal-reset">
      @if ($module['display_reset'])
        <x-form x-target="meal-container notifications meal-reset days-listing months-listing" :action="$module['reset_url']" method="put">
          <button type="submit" class="inline cursor-pointer underline decoration-gray-300 underline-offset-4 transition-colors duration-200 hover:text-blue-600 hover:decoration-blue-400 hover:decoration-[1.15px] dark:decoration-gray-600 dark:hover:text-blue-400 dark:hover:decoration-blue-400">
            {{ __('Reset') }}
          </button>
        </x-form>
      @endif
    </div>
  </x-slot>

  <div id="meal-container" class="space-y-4">
    <div class="space-y-2">
      <p>{{ __('Breakfast') }}</p>
      <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
        <x-button.yes name="breakfast" value="yes" x-target="meal-container notifications meal-reset days-listing months-listing" :action="$module['meal_url']" selected="{{ $module['breakfast'] === 'yes' }}" />
        <x-button.no name="breakfast" value="no" x-target="meal-container notifications meal-reset days-listing months-listing" :action="$module['meal_url']" selected="{{ $module['breakfast'] === 'no' }}" />
      </div>
    </div>

    <div class="space-y-2">
      <p>{{ __('Lunch') }}</p>
      <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
        <x-button.yes name="lunch" value="yes" x-target="meal-container notifications meal-reset days-listing months-listing" :action="$module['meal_url']" selected="{{ $module['lunch'] === 'yes' }}" />
        <x-button.no name="lunch" value="no" x-target="meal-container notifications meal-reset days-listing months-listing" :action="$module['meal_url']" selected="{{ $module['lunch'] === 'no' }}" />
      </div>
    </div>

    <div class="space-y-2">
      <p>{{ __('Dinner') }}</p>
      <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
        <x-button.yes name="dinner" value="yes" x-target="meal-container notifications meal-reset days-listing months-listing" :action="$module['meal_url']" selected="{{ $module['dinner'] === 'yes' }}" />
        <x-button.no name="dinner" value="no" x-target="meal-container notifications meal-reset days-listing months-listing" :action="$module['meal_url']" selected="{{ $module['dinner'] === 'no' }}" />
      </div>
    </div>

    <div class="space-y-2">
      <p>{{ __('Snack') }}</p>
      <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
        <x-button.yes name="snack" value="yes" x-target="meal-container notifications meal-reset days-listing months-listing" :action="$module['meal_url']" selected="{{ $module['snack'] === 'yes' }}" />
        <x-button.no name="snack" value="no" x-target="meal-container notifications meal-reset days-listing months-listing" :action="$module['meal_url']" selected="{{ $module['snack'] === 'no' }}" />
      </div>
    </div>

    <div class="space-y-2">
      <p>{{ __('Meal type') }}</p>
      <div class="grid grid-cols-2 gap-2">
        @foreach ($module['meal_type_options'] as $option)
          <x-form x-target="meal-container notifications meal-reset days-listing months-listing" :action="$module['meal_url']" method="put">
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
      <div class="grid grid-cols-2 gap-2">
        @foreach ($module['social_context_options'] as $option)
          <x-form x-target="meal-container notifications meal-reset days-listing months-listing" :action="$module['meal_url']" method="put">
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
      <x-form x-target="meal-container notifications meal-reset days-listing months-listing" :action="$module['meal_url']" method="put">
        <div class="flex flex-col gap-2">
          <textarea name="notes" rows="2" class="h-auto w-full rounded-md border border-gray-300 px-3 py-2 text-sm shadow-xs placeholder:text-neutral-400 focus:border-indigo-500 focus:ring-1 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" placeholder="{{ __('Anything to remember?') }}">{{ $module['notes'] }}</textarea>
          <div class="flex justify-end">
            <button type="submit" class="rounded-lg border border-gray-200 px-3 py-2 text-sm hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
              {{ __('Save notes') }}
            </button>
          </div>
        </div>
      </x-form>
    </div>
  </div>
</x-module>
