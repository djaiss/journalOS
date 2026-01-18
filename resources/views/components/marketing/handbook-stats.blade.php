<div class="bg-light dark:bg-dark z-10 mb-10">
  <div class="mb-1 flex items-center justify-between">
    <p class="text-xs dark:text-gray-400">Written by...</p>
  </div>
  <div class="pt-1">
    <a href="" class="dark:border-dark text-primary dark:text-primary-dark hover:text-primary dark:hover:text-primary-dark relative flex items-center justify-between rounded border border-gray-400 hover:border-b-[4px] hover:transition-all active:top-[2px]">
      <div class="flex w-full flex-col justify-between gap-1 px-4 py-2">
        <h3 class="mb-0 text-base dark:text-gray-100"><span>Régis Freyd</span></h3>
        <p class="text-primary/50 m-0 line-clamp-1 text-sm leading-tight text-gray-400 dark:text-gray-500">Main maintainer</p>
      </div>
      <div class="flex-shrink-0 px-4 py-2">
        <x-image src="{{ asset('images/marketing/regis.webp') }}" srcset="{{ asset('images/marketing/regis@2x.webp') }} 2x" alt="Regis" width="48" height="48" class="h-12 w-12 rounded-full" />
      </div>
    </a>
  </div>
</div>

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
