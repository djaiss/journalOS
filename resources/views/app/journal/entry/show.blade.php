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
  @if (count($years) > 1)
    <div id="years-listing" class="rounded-tl-lg rounded-tr-lg bg-white dark:bg-gray-900">
      <div class="mx-auto grid divide-x divide-gray-200 border-b border-gray-200 dark:divide-gray-700 dark:border-gray-700" style="grid-template-columns: repeat({{ count($years) }}, minmax(0, 1fr))">
        @foreach ($years as $year)
          <a href="{{ $year->url }}" class="group {{ $year->is_selected ? 'border-indigo-200 bg-indigo-50' : '' }} relative cursor-pointer px-2 py-1 text-center transition-colors first:rounded-tl-lg last:rounded-tr-lg hover:bg-indigo-50">
            <div class="text-sm font-medium text-gray-900">{{ $year->year }}</div>
            <div class="{{ $year->is_selected ? 'scale-x-100' : '' }} absolute bottom-0 left-0 h-0.5 w-full scale-x-0 bg-indigo-600 transition-transform group-hover:scale-x-100"></div>
          </a>
        @endforeach
      </div>
    </div>
  @endif

  <!-- list of months -->
  <div id="months-listing" class="bg-white dark:bg-gray-900">
    <div class="mx-auto grid grid-cols-12 divide-x divide-gray-200 border-b border-gray-200 dark:divide-gray-700 dark:border-gray-700">
      @foreach ($months as $month)
        <a href="{{ $month->url }}" class="group {{ $month->is_selected ? 'border-indigo-200 bg-indigo-50 dark:border-indigo-400/60 dark:bg-indigo-900/40' : '' }} relative cursor-pointer px-2 py-1 text-center transition-colors hover:bg-indigo-50 dark:hover:bg-indigo-900/40">
          <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $month->month_name }}</div>
          <div class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ trans_choice('{0} No entries|{1} :count entry|[2,*] :count entries', $month->entries_count, ['count' => $month->entries_count]) }}</div>
          <div class="{{ $month->is_selected ? 'scale-x-100' : '' }} absolute bottom-0 left-0 h-0.5 w-full scale-x-0 bg-indigo-600 transition-transform group-hover:scale-x-100"></div>
        </a>
      @endforeach
    </div>
  </div>

  <!-- list of days -->
  <div id="days-listing" class="bg-white dark:bg-gray-900">
    <div class="days-grid-{{ $days->count() }} mx-auto grid divide-x divide-gray-200 dark:divide-gray-700">
      @foreach ($days as $day)
        <div class="group {{ $day->is_selected ? 'border-b-indigo-200 bg-indigo-50 dark:border-b-indigo-400/60 dark:bg-indigo-900/40' : '' }} {{ $day->is_today ? 'bg-indigo-50 dark:bg-indigo-900/40' : '' }} relative aspect-square cursor-pointer border-b border-gray-200 text-center transition-colors hover:bg-indigo-50 dark:border-gray-700 dark:hover:bg-indigo-900/40">
          <a href="{{ $day->url }}" data-turbo="false" class="flex h-full flex-col items-center justify-center">
            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $day->day }}</span>
            <div class="{{ $day->has_content === true ? 'bg-green-500' : 'bg-transparent' }} mt-1 h-1.5 w-1.5 rounded-full"></div>
          </a>
        </div>
      @endforeach
    </div>
  </div>

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
