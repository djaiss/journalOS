<?php

/**
 * @var array<string, mixed> $module
 */
?>

<x-module>
  <x-slot:title>{{ __('Shopping') }}</x-slot>
  <x-slot:emoji>🛍️</x-slot>
  <x-slot:action>
    <div id="shopping-reset">
      @if ($module['display_reset'])
        <x-form x-target="shopping-container notifications shopping-reset days-listing months-listing" :action="$module['reset_url']" method="put">
          <button type="submit" class="inline cursor-pointer underline decoration-gray-300 underline-offset-4 transition-colors duration-200 hover:text-blue-600 hover:decoration-blue-400 hover:decoration-[1.15px] dark:decoration-gray-600 dark:hover:text-blue-400 dark:hover:decoration-blue-400">
            {{ __('Reset') }}
          </button>
        </x-form>
      @endif
    </div>
  </x-slot>

  <div id="shopping-container" x-data="{
    showShoppingDetails:
      {{ $module['has_shopped_today'] === 'yes' ? 'true' : 'false' }},
  }">
    <div class="space-y-2">
      <p>{{ __('Shopped today?') }}</p>
      <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
        <x-button.yes name="has_shopped" value="yes" x-target="shopping-container notifications shopping-reset days-listing months-listing" :action="$module['shopping_url']" selected="{{ $module['has_shopped_today'] === 'yes' }}" @click="showShoppingDetails = true" />
        <x-button.no name="has_shopped" value="no" x-target="shopping-container notifications shopping-reset days-listing months-listing" :action="$module['shopping_url']" selected="{{ $module['has_shopped_today'] === 'no' }}" @click="showShoppingDetails = false" />
      </div>
    </div>

    <div x-show="showShoppingDetails" x-cloak class="mt-4 space-y-4">
      <div class="space-y-2">
        <p>{{ __('Shopping type') }}</p>
        <x-form x-target="shopping-container notifications shopping-reset days-listing months-listing" :action="$module['shopping_url']" method="put" id="shopping-type-form">
          <div class="grid grid-cols-2 gap-2">
            @foreach ($module['shopping_types'] as $option)
              <label class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} flex cursor-pointer items-center justify-center rounded-lg border border-gray-200 p-2 text-center hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
                <input type="checkbox" name="shopping_types[]" value="{{ $option['value'] }}" {{ $option['is_selected'] ? 'checked' : '' }} onchange="document.getElementById('shopping-type-form').requestSubmit()" class="hidden" />
                <span>{{ $option['label'] }}</span>
              </label>
            @endforeach
          </div>
        </x-form>
      </div>

      <div class="space-y-2">
        <p>{{ __('Intent') }}</p>
        <div class="grid grid-cols-2 gap-2">
          @foreach ($module['shopping_intents'] as $option)
            <x-form x-target="shopping-container notifications shopping-reset days-listing months-listing" :action="$module['shopping_url']" method="put">
              <input type="hidden" name="shopping_intent" value="{{ $option['value'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} w-full rounded-lg border border-gray-200 p-2 text-center hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
                {{ $option['label'] }}
              </button>
            </x-form>
          @endforeach
        </div>
      </div>

      <div class="space-y-2">
        <p>{{ __('Shopping context') }}</p>
        <div class="grid grid-cols-3 gap-2">
          @foreach ($module['shopping_contexts'] as $option)
            <x-form x-target="shopping-container notifications shopping-reset days-listing months-listing" :action="$module['shopping_url']" method="put">
              <input type="hidden" name="shopping_context" value="{{ $option['value'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} w-full rounded-lg border border-gray-200 p-2 text-center hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
                {{ $option['label'] }}
              </button>
            </x-form>
          @endforeach
        </div>
      </div>

      <div class="space-y-2">
        <p>{{ __('Shopping for') }}</p>
        <div class="grid grid-cols-3 gap-2">
          @foreach ($module['shopping_for_options'] as $option)
            <x-form x-target="shopping-container notifications shopping-reset days-listing months-listing" :action="$module['shopping_url']" method="put">
              <input type="hidden" name="shopping_for" value="{{ $option['value'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} w-full rounded-lg border border-gray-200 p-2 text-center hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
                {{ $option['label'] }}
              </button>
            </x-form>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</x-module>
