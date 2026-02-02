<?php
/**
 * @var \App\Models\Journal $journal
 * @var \App\Models\JournalEntry $entry
 * @var array<int, array<string, mixed>> $reportSections
 * @var \Illuminate\Support\Collection $years
 * @var \Illuminate\Support\Collection $months
 * @var \Illuminate\Support\Collection $days
 */
?>

<x-app-layout :journal="$journal">
  <x-slot:title>
    {{ __('Report') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => $journal->name, 'route' => route('journal.show', ['slug' => $journal->slug]) ],
    ['label' => $entry->getDate(), 'route' => route('journal.entry.show', [
      'slug' => $journal->slug,
      'year' => $entry->year,
      'month' => $entry->month,
      'day' => $entry->day,
    ]) ],
    ['label' => __('Report')]
  ]" />

  <!-- list of years -->
  @if (count($years) > 1)
    <div id="years-listing" class="bg-white dark:bg-gray-900">
      <div class="mx-auto grid divide-x divide-gray-200 border-b border-gray-200 dark:divide-gray-700 dark:border-gray-700" style="grid-template-columns: repeat({{ count($years) }}, minmax(0, 1fr))">
        @foreach ($years as $year)
          <a href="{{ $year->url }}" class="group {{ $year->is_selected ? 'border-indigo-200 bg-indigo-50' : '' }} relative cursor-pointer px-2 py-1 text-center transition-colors hover:bg-indigo-50">
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

  <div class="border-b border-gray-200 bg-gradient-to-br from-white via-indigo-50 to-indigo-100/40 px-4 py-8 dark:border-gray-700 dark:from-gray-900 dark:via-gray-900 dark:to-indigo-950/30">
    <div class="mx-auto flex max-w-6xl flex-col gap-6">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <div class="flex flex-col gap-2">
          <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.2em] text-indigo-600 dark:text-indigo-400">
            <span class="inline-flex h-2 w-2 rounded-full bg-indigo-500"></span>
            {{ __('Journal report') }}
          </div>
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">{{ $entry->getDate() }}</h1>
          <p class="text-sm text-gray-600 dark:text-gray-300">
            {{ __('A narrative snapshot of the day, organized by category and module.') }}
          </p>
        </div>

        <div class="flex flex-wrap gap-3">
          <div class="rounded-2xl border border-indigo-200/70 bg-white px-4 py-3 shadow-sm dark:border-indigo-500/30 dark:bg-gray-900">
            <div class="text-xs font-medium uppercase tracking-[0.18em] text-indigo-500 dark:text-indigo-300">{{ __('Modules') }}</div>
            <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">
              {{ collect($reportSections)->sum(fn ($section) => count($section['modules'])) }}
            </div>
          </div>
          <div class="rounded-2xl border border-indigo-200/70 bg-white px-4 py-3 shadow-sm dark:border-indigo-500/30 dark:bg-gray-900">
            <div class="text-xs font-medium uppercase tracking-[0.18em] text-indigo-500 dark:text-indigo-300">{{ __('Sections') }}</div>
            <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ count($reportSections) }}</div>
          </div>
          <a
            href="{{ route('journal.entry.show', ['slug' => $journal->slug, 'year' => $entry->year, 'month' => $entry->month, 'day' => $entry->day]) }}"
            class="inline-flex items-center justify-center rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-700 shadow-sm transition hover:border-indigo-300 hover:text-indigo-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:border-indigo-400 dark:hover:text-indigo-300"
          >
            {{ __('Back to entry') }}
          </a>
        </div>
      </div>
    </div>
  </div>

  <div class="mx-auto flex max-w-6xl flex-col gap-10 px-4 py-10">
    @foreach ($reportSections as $section)
      <section class="flex flex-col gap-6">
        <div class="flex flex-col gap-2">
          <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
              <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $section['title'] }}</h2>
              <p class="text-sm text-gray-600 dark:text-gray-400">{{ $section['description'] }}</p>
            </div>
            <span class="rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-600 dark:bg-indigo-900/40 dark:text-indigo-300">
              {{ trans_choice('{0} No module|{1} :count module|[2,*] :count modules', count($section['modules']), ['count' => count($section['modules'])]) }}
            </span>
          </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
          @foreach ($section['modules'] as $module)
            <div class="flex flex-col gap-4 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
              <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-3">
                  <span class="text-2xl">{{ $module['emoji'] }}</span>
                  <div class="flex flex-col gap-1">
                    <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">{{ $module['title'] }}</h3>
                    @if ($module['summary'])
                      <p class="text-sm text-gray-500 dark:text-gray-400">{{ $module['summary'] }}</p>
                    @endif
                  </div>
                </div>
              </div>

              <div class="flex flex-col gap-3">
                @foreach ($module['items'] as $item)
                  <div class="flex flex-col gap-1 rounded-xl border border-gray-100 bg-gray-50 px-4 py-3 dark:border-gray-800 dark:bg-gray-950">
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-gray-500 dark:text-gray-400">{{ $item['question'] }}</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $item['answer'] }}</p>
                  </div>
                @endforeach
              </div>
            </div>
          @endforeach
        </div>
      </section>
    @endforeach
  </div>
</x-app-layout>
