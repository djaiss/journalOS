@props(['emoji', 'name'])

<div class="rounded-lg border border-gray-200 dark:border-gray-700">
  <div class="rounded-t-lg border-b border-gray-200 bg-gray-50 px-4 py-2 dark:border-gray-700 dark:bg-gray-800">
    <h2 class="font-semibold dark:text-gray-100">
      <span class="mr-1">{{ $emoji }}</span>
      {{ $name }}
    </h2>
  </div>

  <div class="flex items-start space-y-3 space-x-2 p-4">
    <p><x-phosphor-fingerprint class="relative top-1 h-4 w-4 text-gray-500 dark:text-gray-400" /></p>

    <div class="space-y-1">
      <p class="text-gray-600 dark:text-gray-300">Tracked data</p>

      <ul class="text-sm">
        {{ $trackedData ?? '' }}
      </ul>
    </div>
  </div>
</div>
