<x-marketing.written-by-regis />

<div class="mb-10 flex flex-col gap-y-2 text-sm">
  <div class="flex items-center gap-x-2">
    <x-phosphor-chart-line-up class="h-4 w-4 text-gray-500 dark:text-gray-400" />
    <p class="text-gray-600 dark:text-gray-400">{{ $stats['word_count'] }} words</p>
  </div>
  <div class="flex items-center gap-x-2">
    <x-phosphor-clock class="h-4 w-4 text-gray-500 dark:text-gray-400" />
    <p class="text-gray-600 dark:text-gray-400">{{ $stats['reading_time'] }} minutes</p>
  </div>
  <div>
    <p class="text-gray-600 dark:text-gray-400">
      This represents only
      <code class="rounded-md border border-gray-200 px-1 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ $stats['comparison']['percentage'] }}%</code>
      of the number of words in
      <span class="font-semibold">{{ $stats['comparison']['title'] }}</span>
      by
      <span class="font-semibold">{{ $stats['comparison']['author'] }}</span>
    </p>
  </div>
</div>

<div class="text-sm">
  <div class="mb-2 flex items-center gap-x-2">
    <x-phosphor-lightbulb class="h-4 w-4 text-amber-500 dark:text-amber-400" />
    <h3 class="font-semibold dark:text-gray-100">Random fact, so we don't die stupid</h3>
  </div>

  <p class="text-gray-600 dark:text-gray-400">{{ $stats['random_fact'] }}</p>
</div>
