@props([
  'emoji',
  'name',
  'category',
])

<div class="rounded-lg border border-gray-200 dark:border-gray-700">
  <!-- emoji + name -->
  <div class="rounded-t-lg border-b border-gray-200 bg-gray-50 px-4 py-2 dark:border-gray-700 dark:bg-gray-800">
    <h2 class="font-semibold dark:text-gray-100">
      <span class="mr-1">{{ $emoji }}</span>
      {{ $name }}
    </h2>
  </div>

  <!-- information -->
  <div class="flex items-start space-x-2 border-b border-gray-200 p-4 dark:border-gray-700">
    <x-phosphor-fingerprint class="mt-1 h-4 w-4 shrink-0 text-gray-500 dark:text-gray-400" />
    <div class="space-y-1">
      <p class="text-gray-600 dark:text-gray-300">Tracked data</p>

      <ul class="text-sm">
        {{ $trackedData ?? '' }}
      </ul>
    </div>
  </div>

  <!-- category -->
  <div class="flex items-center space-x-2 px-4 py-1 text-xs">
    <x-phosphor-folder-open class="h-4 w-4 shrink-0 text-gray-500 dark:text-gray-400" />
    <div class="space-y-1">
      <x-tooltip text="Name of the category"><p>{{ $category }}</p></x-tooltip>
    </div>
  </div>
</div>
