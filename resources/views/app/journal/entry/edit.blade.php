<?php
/**
 * @var \App\Models\Journal $journal
 * @var \App\Models\JournalEntry $entry
 * @var array<int, array<int, array{key: string, view: string, data: array<string, mixed>}>> $columns
 * @var array<string, mixed> $notes
 * @var int $layoutColumnsCount
 * @var \Illuminate\Support\Collection $years
 * @var \Illuminate\Support\Collection $months
 * @var \Illuminate\Support\Collection $days
 */
?>

<x-app-layout :journal="$journal">
  <x-slot:title>
    {{ __('Journal') }}
  </x-slot>

  <!-- list of years -->
  @include('app.journal.entry.partials.years', ['years' => $years])

  <!-- list of months -->
  @include('app.journal.entry.partials.months', ['months' => $months])

  <!-- list of days -->
  @include('app.journal.entry.partials.days', ['days' => $days])

  <!-- lock status -->
  @if (! $entry->isEditable())
    <div class="border-b border-gray-200 bg-gradient-to-r from-amber-50 to-orange-50 px-4 py-4 dark:border-gray-700 dark:from-amber-900/20 dark:to-orange-900/20">
      <div class="mx-auto max-w-3xl">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/40">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 dark:text-amber-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="flex-1">
            <h3 class="text-sm font-semibold text-amber-900 dark:text-amber-200">{{ __('This entry is locked') }}</h3>
            <p class="mt-0.5 text-xs text-amber-700 dark:text-amber-300">{{ __('Entries older than 48 hours cannot be modified to preserve the authenticity of your journal.') }}</p>
          </div>
        </div>
      </div>
    </div>
  @else
  <div class="flex justify-end border-b border-gray-200 px-4 py-2 dark:border-gray-700 dark:from-amber-900/20 dark:to-orange-900/20">
    <a href="{{ route('journal.entry.toggle-edit', ['slug' => $journal->slug, 'year' => $entry->year, 'month' => $entry->month, 'day' => $entry->day]) }}" class="inline-block">
      <x-toggle name="edit-mode" :checked="true">
        {{ __('Edit mode') }}
      </x-toggle>
    </a>
  </div>
  @endif

  <!-- entry content -->
  @php
    $columnCount = $layoutColumnsCount > 0 ? $layoutColumnsCount : count($columns);
    $gridColumns = $columnCount + 1;
  @endphp

  <div class="grid gap-4 rounded-b-lg bg-gray-50 dark:bg-gray-950" style="grid-template-columns: repeat({{ $gridColumns }}, minmax(0, 1fr))">
    @foreach ($columns as $column)
      <div class="{{ $loop->first ? 'pl-4' : '' }} py-4">
        <div class="space-y-2">
          @foreach ($column as $module)
            @include($module['view'], $module['data'])
          @endforeach
        </div>
      </div>
    @endforeach

    <div class="flex h-full border-l border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
      @include('app.journal.entry.partials.note', ['module' => $notes])
    </div>
  </div>
</x-app-layout>
