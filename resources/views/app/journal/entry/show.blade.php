<?php
/**
 * @var \App\Models\Journal $journal
 * @var \App\Models\JournalEntry $entry
 * @var array<string, mixed> $modules
 * @var \Illuminate\Support\Collection $years
 * @var \Illuminate\Support\Collection $months
 * @var \Illuminate\Support\Collection $days
 */
?>

<x-app-layout :journal="$journal">
  <x-slot:title>
    {{ __('Journal') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => $journal->name]
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
  <div class="grid grid-cols-4 gap-4">
    <!-- Life lane -->
    <div class="py-4 pl-4">
      <div class="mb-4">
        <h3 class="text-md font-semibold">{{ __('Life') }}</h3>
        <p class="text-gray-600 dark:text-gray-400">{{ __('What happened today') }}</p>
      </div>

      <!-- modules -->
      <div class="space-y-2">
        @if ($journal->show_sleep_module)
          @include('app.journal.entry.partials.sleep', ['module' => $modules['sleep']])
        @endif

        @if ($journal->show_travel_module)
          @include('app.journal.entry.partials.travel', ['module' => $modules['travel']])
        @endif

        @if ($journal->show_shopping_module)
          @include('app.journal.entry.partials.shopping', ['module' => $modules['shopping']])
        @endif

        @if ($journal->show_kids_module)
          @include('app.journal.entry.partials.kids', ['module' => $modules['kids'], 'entry' => $entry])
        @endif
      </div>
    </div>

    <!-- Day lane -->
    <div class="py-4">
      <div class="mb-4">
        <h3 class="text-md font-semibold">{{ __('Day') }}</h3>
        <p class="text-gray-600 dark:text-gray-400">{{ __('What shaped the day') }}</p>
      </div>

      <!-- modules -->
      <div class="space-y-2">
        @if ($journal->show_day_type_module)
          @include('app.journal.entry.partials.day_type', ['module' => $modules['day_type']])
        @endif

        @if ($journal->show_primary_obligation_module)
          @include('app.journal.entry.partials.primary_obligation', ['module' => $modules['primary_obligation']])
        @endif

        @if ($journal->show_work_module)
          @include('app.journal.entry.partials.work', ['module' => $modules['work']])
        @endif

        @if ($journal->show_health_module)
          @include('app.journal.entry.partials.health', ['module' => $modules['health']])
        @endif

        @if ($journal->show_hygiene_module)
          @include('app.journal.entry.partials.hygiene', ['module' => $modules['hygiene']])
        @endif

        @if ($journal->show_mood_module)
          @include('app.journal.entry.partials.mood', ['module' => $modules['mood']])
        @endif

        @if ($journal->show_energy_module)
          @include('app.journal.entry.partials.energy', ['module' => $modules['energy']])
        @endif

        @if ($journal->show_social_density_module)
          @include('app.journal.entry.partials.social_density', ['module' => $modules['social_density']])
        @endif
      </div>
    </div>

    <!-- Leisure lane -->
    <div class="py-4">
      <div class="mb-4">
        <h3 class="text-md font-semibold">{{ __('Leisure') }}</h3>
        <p class="text-gray-600 dark:text-gray-400">{{ __('What you did for yourself') }}</p>
      </div>

      <!-- modules -->
      <div class="space-y-2">
        @if ($journal->show_physical_activity_module)
          @include('app.journal.entry.partials.physical_activity', ['module' => $modules['physical_activity']])
        @endif

        @if ($journal->show_sexual_activity_module)
          @include('app.journal.entry.partials.sexual_activity', ['module' => $modules['sexual_activity'], 'entry' => $entry])
        @endif
      </div>
    </div>

    <div class="flex h-full border-l border-gray-200 bg-white">
      @include('app.journal.entry.partials.note', ['entry' => $entry, 'module' => $modules['notes']])
    </div>
  </div>
</x-app-layout>
