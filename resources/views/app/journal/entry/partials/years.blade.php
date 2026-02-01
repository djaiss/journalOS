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
