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
