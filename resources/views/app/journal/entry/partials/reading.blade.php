<?php

/**
 * @var array<string, mixed> $module
 */
?>

<x-module>
  <x-slot:title>{{ __('Reading') }}</x-slot>
  <x-slot:emoji>📚</x-slot>
  <x-slot:action>
    <div id="reading-reset">
      @if ($module['display_reset'])
        <x-form x-target="reading-container notifications reading-reset days-listing months-listing" :action="$module['reset_url']" method="put">
          <button type="submit" class="inline cursor-pointer underline decoration-gray-300 underline-offset-4 transition-colors duration-200 hover:text-blue-600 hover:decoration-blue-400 hover:decoration-[1.15px] dark:decoration-gray-600 dark:hover:text-blue-400 dark:hover:decoration-blue-400">
            {{ __('Reset') }}
          </button>
        </x-form>
      @endif
    </div>
  </x-slot>

  <div id="reading-container" x-data="{
    showReadingDetails:
      {{ $module['did_read_today'] === 'yes' ? 'true' : 'false' }},
  }">
    <div class="space-y-2">
      <p>{{ __('Did I read today?') }}</p>
      <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
        <x-button.yes name="did_read_today" value="yes" x-target="reading-container notifications reading-reset days-listing months-listing" :action="$module['reading_url']" selected="{{ $module['did_read_today'] === 'yes' }}" @click="showReadingDetails = true" />
        <x-button.no name="did_read_today" value="no" x-target="reading-container notifications reading-reset days-listing months-listing" :action="$module['reading_url']" selected="{{ $module['did_read_today'] === 'no' }}" @click="showReadingDetails = false" />
      </div>
    </div>

    <div x-show="showReadingDetails" x-cloak class="mt-4 space-y-4">
      <div class="space-y-2">
        <p>{{ __('Books read') }}</p>
        <x-form x-target="reading-container notifications reading-reset days-listing months-listing" :action="$module['books_url']" method="post" class="flex flex-col gap-2 sm:flex-row">
          <div class="flex-1">
            <input type="text" name="book_name" list="reading-book-suggestions" class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100" placeholder="{{ __('Add a book') }}" />
            <datalist id="reading-book-suggestions">
              @foreach ($module['book_suggestions'] as $bookName)
                <option value="{{ $bookName }}"></option>
              @endforeach
            </datalist>
          </div>
          <button type="submit" class="cursor-pointer rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
            {{ __('Add') }}
          </button>
        </x-form>

        @if ($module['books'] !== [])
          <div class="flex flex-col gap-2">
            @foreach ($module['books'] as $book)
              <div class="flex items-center justify-between rounded-lg border border-gray-200 px-3 py-2 dark:border-gray-700">
                <span class="text-sm text-gray-900 dark:text-gray-100">{{ $book['name'] }}</span>
                <x-form x-target="reading-container notifications reading-reset days-listing months-listing" :action="route('journal.entry.reading.books.destroy', ['slug' => $module['slug'], 'year' => $module['year'], 'month' => $module['month'], 'day' => $module['day'], 'book' => $book['id']])" method="delete">
                  <button type="submit" class="text-xs font-medium text-gray-500 underline decoration-gray-300 underline-offset-4 transition-colors duration-200 hover:text-red-600 hover:decoration-red-400 dark:text-gray-400 dark:decoration-gray-600 dark:hover:text-red-400">
                    {{ __('Remove') }}
                  </button>
                </x-form>
              </div>
            @endforeach
          </div>
        @else
          <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('No books logged yet.') }}</p>
        @endif
      </div>

      <div class="space-y-2">
        <p>{{ __('How much did I read?') }}</p>
        <div class="grid grid-cols-2 gap-2">
          @foreach ($module['reading_amounts'] as $option)
            <x-form x-target="reading-container notifications reading-reset days-listing months-listing" :action="$module['reading_url']" method="put">
              <input type="hidden" name="reading_amount" value="{{ $option['value'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} w-full rounded-lg border border-gray-200 p-2 text-center hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
                {{ $option['label'] }}
              </button>
            </x-form>
          @endforeach
        </div>
      </div>

      <div class="space-y-2">
        <p>{{ __('Mental state after reading') }}</p>
        <div class="grid grid-cols-2 gap-2">
          @foreach ($module['mental_states'] as $option)
            <x-form x-target="reading-container notifications reading-reset days-listing months-listing" :action="$module['reading_url']" method="put">
              <input type="hidden" name="mental_state" value="{{ $option['value'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} w-full rounded-lg border border-gray-200 p-2 text-center hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
                {{ $option['label'] }}
              </button>
            </x-form>
          @endforeach
        </div>
      </div>

      <div class="space-y-2">
        <p>{{ __('Reading felt…') }}</p>
        <div class="grid grid-cols-2 gap-2">
          @foreach ($module['reading_feels'] as $option)
            <x-form x-target="reading-container notifications reading-reset days-listing months-listing" :action="$module['reading_url']" method="put">
              <input type="hidden" name="reading_feel" value="{{ $option['value'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} w-full rounded-lg border border-gray-200 p-2 text-center hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
                {{ $option['label'] }}
              </button>
            </x-form>
          @endforeach
        </div>
      </div>

      <div class="space-y-2">
        <p>{{ __('Did I want to continue reading?') }}</p>
        <div class="grid grid-cols-3 gap-2">
          @foreach ($module['want_continue_options'] as $option)
            <x-form x-target="reading-container notifications reading-reset days-listing months-listing" :action="$module['reading_url']" method="put">
              <input type="hidden" name="want_continue" value="{{ $option['value'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold dark:bg-green-900/40' : '' }} w-full rounded-lg border border-gray-200 p-2 text-center hover:bg-green-50 dark:border-gray-700 dark:hover:bg-green-900/40">
                {{ $option['label'] }}
              </button>
            </x-form>
          @endforeach
        </div>
      </div>

      <div class="space-y-2">
        <p>{{ __('What limited my reading today?') }}</p>
        <div class="grid grid-cols-2 gap-2">
          @foreach ($module['reading_limits'] as $option)
            <x-form x-target="reading-container notifications reading-reset days-listing months-listing" :action="$module['reading_url']" method="put">
              <input type="hidden" name="reading_limit" value="{{ $option['value'] }}" />
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
