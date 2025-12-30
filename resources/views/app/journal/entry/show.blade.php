<x-app-layout :journal="$journal">
  <x-slot:title>
    {{ __('Journal') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => $journal->name]
  ]" />

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

  <div id="days-listing" class="bg-white dark:bg-gray-900">
    <div class="days-grid-{{ $days->count() }} mx-auto grid divide-x divide-gray-200 dark:divide-gray-700">
      @foreach ($days as $day)
        <div class="group {{ $day->is_selected ? 'border-b-indigo-200 bg-indigo-50 dark:border-b-indigo-400/60 dark:bg-indigo-900/40' : '' }} {{ $day->is_today ? 'bg-indigo-50 dark:bg-indigo-900/40' : '' }} relative aspect-square cursor-pointer border-b border-gray-200 text-center transition-colors hover:bg-indigo-50 dark:border-gray-700 dark:hover:bg-indigo-900/40">
          <a href="{{ $day->url }}" data-turbo="false" class="flex h-full flex-col items-center justify-center">
            <span class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $day->day }}</span>
            <div class="{{ $day->has_blocks === true ? 'bg-green-500' : 'bg-transparent' }} mt-1 h-1.5 w-1.5 rounded-full"></div>
          </a>
        </div>
      @endforeach
    </div>
  </div>

  <div class="grid grid-cols-3 gap-4 p-4">
    <div>
      <!-- title -->
      <div class="mb-4">
        <h3 class="text-md font-semibold">{{ __('Life') }}</h3>
        <p class="text-gray-600 dark:text-gray-400">{{ __('What happened today') }}</p>
      </div>

      <!-- modules -->
      <div class="space-y-2">
        @if ($journal->show_sleep_module)
          @include('app.journal.entry.partials.sleep', ['module' => $modules['sleep']])
        @endif
      </div>
    </div>
    <div>
      <!-- title -->
      <div class="mb-4">
        <h3 class="text-md font-semibold">{{ __('Work') }}</h3>
        <p class="text-gray-600 dark:text-gray-400">{{ __('What you worked on') }}</p>
      </div>

      <!-- modules -->
      <div class="space-y-2">
        @if ($journal->show_sleep_module)
          @include('app.journal.entry.partials.sleep', ['module' => $modules['sleep']])
        @endif
      </div>
    </div>
    <div>
      <!-- title -->
      <div class="mb-4">
        <h3 class="text-md font-semibold">{{ __('Mood') }}</h3>
        <p class="text-gray-600 dark:text-gray-400">{{ __('How the day felt') }}</p>
      </div>

      <!-- modules -->
      <div class="space-y-2">
        @if ($journal->show_sleep_module)
          @include('app.journal.entry.partials.sleep', ['module' => $modules['sleep']])
        @endif
      </div>
    </div>
  </div>
</x-app-layout>
