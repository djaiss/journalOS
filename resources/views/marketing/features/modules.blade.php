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

          <x-marketing.module emoji="🌖" name="Sleep">
            <x-slot:trackedData>
              <li>Time you started your nap</li>
              <li>Time you ended your nap</li>
            </x-slot:trackedData>
          </x-marketing.module>
        </div>

        <div class="">b</div>
        <div class="">v</div>
      </div>
    </div>
  </div>
</x-marketing-layout>
