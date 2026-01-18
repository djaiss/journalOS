<?php
/**
 * No view data.
 */
?>

<x-marketing-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Features', 'route' => route('marketing.features.modules')],
  ['label' => 'Modules'],
]">
  <div class="relative bg-white dark:bg-gray-900">
    <div class="mx-auto max-w-7xl px-6 py-8 sm:pt-20 sm:pb-8 lg:px-8 xl:px-0">
      <!-- title -->
      <div class="mb-20 text-center">
        <h2 class="mb-6 text-4xl font-semibold tracking-tight text-gray-900 sm:text-6xl dark:text-gray-100">All the modules you can use in your journal</h2>
        <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">We have 423 modules to track anything you can think of.</p>
      </div>

      <!-- module list -->
      <div class="grid grid-cols-3 gap-2">
        <div class="">
          <div class="rounded-lg border border-gray-200 dark:border-gray-700">
            <div class="rounded-t-lg border-b border-gray-200 bg-gray-50 px-4 py-2 dark:border-gray-700 dark:bg-gray-800">
              <h2 class="font-semibold dark:text-gray-100">
                <span class="mr-1">🌖</span>
                Sleep
              </h2>
            </div>

            <div class="flex items-start space-y-3 space-x-2 p-4">
              <p><x-phosphor-fingerprint class="relative top-1 h-4 w-4 text-gray-500 dark:text-gray-400" /></p>
              <div class="space-y-1">
                <p class="text-gray-600 dark:text-gray-300">Tracked data</p>
                <ul class="text-sm">
                  <li>Time you went to sleep</li>
                  <li>Time you woke up</li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <div class="">b</div>
        <div class="">v</div>
      </div>
    </div>
  </div>
</x-marketing-layout>
