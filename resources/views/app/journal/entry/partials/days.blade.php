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
