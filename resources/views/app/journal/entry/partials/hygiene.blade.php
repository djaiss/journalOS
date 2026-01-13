<?php
/**
 * @var array<string, mixed> $module
 */
?>

<x-module>
  <x-slot:title>{{ __('Hygiene') }}</x-slot>
  <x-slot:emoji>🧼</x-slot>
  <x-slot:action>
    <div id="hygiene-reset">
      @if ($module['display_reset'])
        <x-form x-target="hygiene-container notifications hygiene-reset days-listing months-listing" :action="$module['reset_url']" method="put">
          <button type="submit" class="inline cursor-pointer underline decoration-gray-300 underline-offset-4 transition-colors duration-200 hover:text-blue-600 hover:decoration-blue-400 hover:decoration-[1.15px] dark:decoration-gray-600 dark:hover:text-blue-400 dark:hover:decoration-blue-400">
            {{ __('Reset') }}
          </button>
        </x-form>
      @endif
    </div>
  </x-slot>

  <div id="hygiene-container">
    <div class="flex flex-col gap-4">
      <div class="flex flex-col gap-2">
        <p>{{ __('Showered') }}</p>
        <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
          <x-button.yes name="showered" value="yes" x-target="hygiene-container notifications hygiene-reset days-listing months-listing" :action="$module['hygiene_url']" selected="{{ $module['showered'] === 'yes' }}" />
          <x-button.no name="showered" value="no" x-target="hygiene-container notifications hygiene-reset days-listing months-listing" :action="$module['hygiene_url']" selected="{{ $module['showered'] === 'no' }}" />
        </div>
      </div>

      <div class="flex flex-col gap-2">
        <p>{{ __('Brushed teeth') }}</p>
        <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
          @foreach ($module['brushed_teeth_options'] as $option)
            <div class="flex-1 border-r border-gray-200 first:overflow-hidden first:rounded-l-lg last:rounded-r-lg last:border-r-0 dark:border-gray-700">
              <x-form x-target="hygiene-container notifications hygiene-reset days-listing months-listing" :action="$module['hygiene_url']" method="put">
                <input type="hidden" name="brushed_teeth" value="{{ $option['value'] }}" />
                <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} flex h-full w-full cursor-pointer items-center justify-center p-2 text-center hover:bg-green-50 dark:hover:bg-green-900/40">
                  {{ $option['label'] }}
                </button>
              </x-form>
            </div>
          @endforeach
        </div>
      </div>

      <div class="flex flex-col gap-2">
        <p>{{ __('Skincare') }}</p>
        <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
          <x-button.yes name="skincare" value="yes" x-target="hygiene-container notifications hygiene-reset days-listing months-listing" :action="$module['hygiene_url']" selected="{{ $module['skincare'] === 'yes' }}" />
          <x-button.no name="skincare" value="no" x-target="hygiene-container notifications hygiene-reset days-listing months-listing" :action="$module['hygiene_url']" selected="{{ $module['skincare'] === 'no' }}" />
        </div>
      </div>
    </div>
  </div>
</x-module>
