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
        <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">We have 18 modules to track different aspects of your daily life.</p>
      </div>

      <!-- module list -->
      <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
        <!-- first row -->
        <div class="space-y-4">
          <x-marketing.module emoji="😴" name="Sleep">
            <x-slot:trackedData>
              <li>Bedtime</li>
              <li>Wake up time</li>
              <li>Sleep duration</li>
            </x-slot>
            <x-slot:category>Body & Health</x-slot:category>
          </x-marketing.module>

          <x-marketing.module emoji="😊" name="Mood">
            <x-slot:trackedData>
              <li>Overall mood</li>
            </x-slot>
          </x-marketing.module>

          <x-marketing.module emoji="⚡" name="Energy">
            <x-slot:trackedData>
              <li>Energy level</li>
            </x-slot>
          </x-marketing.module>

          <x-marketing.module emoji="💼" name="Work">
            <x-slot:trackedData>
              <li>Did you work?</li>
              <li>Work mode (remote/on-site/hybrid)</li>
              <li>Workload</li>
              <li>Did you procrastinate?</li>
            </x-slot>
          </x-marketing.module>

          <x-marketing.module emoji="🌤️" name="Weather">
            <x-slot:trackedData>
              <li>Weather condition</li>
              <li>Temperature range</li>
              <li>Precipitation</li>
              <li>Daylight duration</li>
            </x-slot>
          </x-marketing.module>

          <x-marketing.module emoji="🌦️" name="Weather Influence">
            <x-slot:trackedData>
              <li>Mood effect</li>
              <li>Energy effect</li>
              <li>Plans influence</li>
              <li>Time spent outside</li>
            </x-slot>
          </x-marketing.module>
        </div>

        <!-- second row -->
        <div class="space-y-4">
          <x-marketing.module emoji="🛒" name="Shopping">
            <x-slot:trackedData>
              <li>Did you shop?</li>
              <li>Shopping type</li>
              <li>Shopping intent (planned/impulse)</li>
              <li>Shopping context</li>
              <li>Shopping for whom</li>
            </x-slot>
          </x-marketing.module>

          <x-marketing.module emoji="🍽️" name="Meals">
            <x-slot:trackedData>
              <li>Meal presence</li>
              <li>Meal type</li>
              <li>Social context</li>
              <li>Notes</li>
            </x-slot>
          </x-marketing.module>

          <x-marketing.module emoji="👶" name="Kids">
            <x-slot:trackedData>
              <li>Had kids today?</li>
            </x-slot>
          </x-marketing.module>

          <x-marketing.module emoji="📅" name="Day Type">
            <x-slot:trackedData>
              <li>Type of day (workday/weekend/vacation/sick)</li>
            </x-slot>
          </x-marketing.module>

          <x-marketing.module emoji="🎯" name="Primary Obligation">
            <x-slot:trackedData>
              <li>What demanded most attention</li>
            </x-slot>
          </x-marketing.module>

          <x-marketing.module emoji="🏃" name="Physical Activity">
            <x-slot:trackedData>
              <li>Did physical activity?</li>
              <li>Activity type</li>
              <li>Activity intensity</li>
            </x-slot>
          </x-marketing.module>
        </div>

        <!-- third row -->
        <div class="space-y-4">
          <x-marketing.module emoji="🏥" name="Health">
            <x-slot:trackedData>
              <li>Health status</li>
            </x-slot>
          </x-marketing.module>

          <x-marketing.module emoji="🧼" name="Hygiene">
            <x-slot:trackedData>
              <li>Showered?</li>
              <li>Brushed teeth?</li>
              <li>Skincare?</li>
            </x-slot>
          </x-marketing.module>

          <x-marketing.module emoji="❤️" name="Sexual Activity">
            <x-slot:trackedData>
              <li>Had sexual activity?</li>
              <li>Sexual activity type</li>
            </x-slot>
          </x-marketing.module>

          <x-marketing.module emoji="🧠" name="Cognitive Load">
            <x-slot:trackedData>
              <li>Cognitive load level</li>
              <li>Primary source</li>
              <li>Load quality</li>
            </x-slot>
          </x-marketing.module>

          <x-marketing.module emoji="👥" name="Social Density">
            <x-slot:trackedData>
              <li>Social density level</li>
            </x-slot>
          </x-marketing.module>

          <x-marketing.module emoji="✈️" name="Travel">
            <x-slot:trackedData>
              <li>Did you travel?</li>
              <li>Travel details</li>
              <li>Travel mode</li>
            </x-slot>
          </x-marketing.module>
        </div>
      </div>
    </div>
  </div>
</x-marketing-layout>
