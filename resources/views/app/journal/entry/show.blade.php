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

  <div class="grid grid-cols-4 gap-4 p-4">
    <!-- Life lane -->
    <div>
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
      </div>
    </div>

    <!-- Day lane -->
    <div>
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
          @include(
            'app.journal.entry.partials.work',
            [
              'module' => $modules['work'],
              'entry' => $entry,
            ]
          )
        @endif

        @if ($journal->show_health_module)
          @include('app.journal.entry.partials.health', ['module' => $modules['health']])
        @endif

        @if ($journal->show_mood_module)
          @include('app.journal.entry.partials.mood', ['module' => $modules['mood']])
        @endif

        @if ($journal->show_energy_module)
          @include('app.journal.entry.partials.energy', ['module' => $modules['energy']])
        @endif
      </div>
    </div>

    <!-- Leisure lane -->
    <div>
      <div class="mb-4">
        <h3 class="text-md font-semibold">{{ __('Leisure') }}</h3>
        <p class="text-gray-600 dark:text-gray-400">{{ __('What you did for yourself') }}</p>
      </div>

      <!-- modules -->
      <div class="space-y-2">
        @if ($journal->show_physical_activity_module)
          @include('app.journal.entry.partials.physical_activity', ['module' => $modules['physical_activity'], 'entry' => $entry])
        @endif

        @if ($journal->show_sexual_activity_module)
          @include('app.journal.entry.partials.sexual_activity', ['module' => $modules['sexual_activity'], 'entry' => $entry])
        @endif
      </div>
    </div>
  </div>
</x-app-layout>
