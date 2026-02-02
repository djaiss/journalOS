<?php

/**
 * No view data.
 */
?>

<x-marketing-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Modules'],
]">
  <div class="relative bg-white dark:bg-gray-900" x-data="{
    selectedCategory: 'All',
    searchQuery: '',
    categories: [
      { name: 'All', emoji: '📋', count: 20 },
      { name: 'Body & Health', emoji: '💪', count: 5 },
      { name: 'Mind & Emotions', emoji: '🧠', count: 4 },
      { name: 'Work', emoji: '💼', count: 3 },
      { name: 'Social', emoji: '👥', count: 4 },
      { name: 'Places', emoji: '📍', count: 4 },
    ],
  }">
    <div class="mx-auto max-w-7xl px-6 py-8 sm:pt-20 sm:pb-8 lg:px-8 xl:px-0">
      <!-- title -->
      <div class="mb-20 text-center">
        <h2 class="mb-6 text-4xl font-semibold tracking-tight text-gray-900 sm:text-6xl dark:text-gray-100">All the modules you can use in your journal</h2>
        <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">We have 20 modules to track different aspects of your daily life.</p>
      </div>

      <!-- tabs filter + search field -->
      <div class="mb-12 flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
        <!-- tabs containing categories -->
        <div class="flex flex-wrap gap-2">
          <template x-for="category in categories" :key="category.name">
            <button
              @click="selectedCategory = category.name"
              :class="{
                'bg-blue-600 text-white dark:bg-blue-500': selectedCategory === category.name,
                'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-gray-700': selectedCategory !== category.name
              }"
              class="cursor-pointer rounded-lg px-4 py-2 text-sm font-medium transition-colors">
              <span x-text="category.emoji" class="mr-1"></span>
              <span x-text="category.name"></span>
              <span x-text="'(' + category.count + ')'" class="ml-1 opacity-70"></span>
            </button>
          </template>
        </div>

        <!-- search field -->
        <div class="relative w-full sm:w-80">
          <input type="text" x-model="searchQuery" placeholder="Search modules..." class="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 pl-10 text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none dark:border-gray-700 dark:bg-gray-800 dark:text-gray-100 dark:placeholder-gray-400" />
          <x-phosphor-magnifying-glass class="absolute top-1/2 left-3 h-4 w-4 -translate-y-1/2 text-gray-400" />
        </div>
      </div>

      <!-- module list -->
      <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
        <!-- first row -->
        <div class="space-y-4">
          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Body & Health') &&
              (searchQuery === '' || 'sleep'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="😴" name="Sleep">
              <x-slot:trackedData>
                <li>Bedtime</li>
                <li>Wake up time</li>
                <li>Sleep duration</li>
              </x-slot>
              <x-slot:category>Body & Health</x-slot>
            </x-marketing.module>
          </div>

          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Mind & Emotions') &&
              (searchQuery === '' || 'mood'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="😊" name="Mood">
              <x-slot:trackedData>
                <li>Overall mood</li>
              </x-slot>
              <x-slot:category>Mind & Emotions</x-slot>
            </x-marketing.module>
          </div>

          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Mind & Emotions') &&
              (searchQuery === '' || 'energy'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="⚡" name="Energy">
              <x-slot:trackedData>
                <li>Energy level</li>
              </x-slot>
              <x-slot:category>Mind & Emotions</x-slot>
            </x-marketing.module>
          </div>

          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Mind & Emotions') &&
              (searchQuery === '' || 'reading'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="📚" name="Reading">
              <x-slot:trackedData>
                <li>Books read</li>
                <li>Reading amount</li>
                <li>Mental state</li>
                <li>Reading feel</li>
                <li>Reading limits</li>
              </x-slot>
              <x-slot:category>Mind & Emotions</x-slot>
            </x-marketing.module>
          </div>

          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Work') &&
              (searchQuery === '' || 'work'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="💼" name="Work">
              <x-slot:trackedData>
                <li>Did you work?</li>
                <li>Work mode (remote/on-site/hybrid)</li>
                <li>Workload</li>
                <li>Did you procrastinate?</li>
              </x-slot>
              <x-slot:category>Work</x-slot>
            </x-marketing.module>
          </div>

          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Places') &&
              (searchQuery === '' || 'weather'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="🌤️" name="Weather">
              <x-slot:trackedData>
                <li>Weather condition</li>
                <li>Temperature range</li>
                <li>Precipitation</li>
                <li>Daylight duration</li>
              </x-slot>
              <x-slot:category>Places</x-slot>
            </x-marketing.module>
          </div>

          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Places') &&
              (searchQuery === '' ||
                'weather influence'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="🌦️" name="Weather Influence">
              <x-slot:trackedData>
                <li>Mood effect</li>
                <li>Energy effect</li>
                <li>Plans influence</li>
                <li>Time spent outside</li>
              </x-slot>
              <x-slot:category>Places</x-slot>
            </x-marketing.module>
          </div>
        </div>

        <!-- second row -->
        <div class="space-y-4">
          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Places') &&
              (searchQuery === '' || 'shopping'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="🛒" name="Shopping">
              <x-slot:trackedData>
                <li>Did you shop?</li>
                <li>Shopping type</li>
                <li>Shopping intent (planned/impulse)</li>
                <li>Shopping context</li>
                <li>Shopping for whom</li>
              </x-slot>
              <x-slot:category>Places</x-slot>
            </x-marketing.module>
          </div>

          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Body & Health') &&
              (searchQuery === '' || 'meals'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="🍽️" name="Meals">
              <x-slot:trackedData>
                <li>Meal presence</li>
                <li>Meal type</li>
                <li>Social context</li>
                <li>Notes</li>
              </x-slot>
              <x-slot:category>Body & Health</x-slot>
            </x-marketing.module>
          </div>

          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Social') &&
              (searchQuery === '' || 'kids'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="👶" name="Kids">
              <x-slot:trackedData>
                <li>Had kids today?</li>
              </x-slot>
              <x-slot:category>Social</x-slot>
            </x-marketing.module>
          </div>

          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Work') &&
              (searchQuery === '' || 'day type'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="📅" name="Day Type">
              <x-slot:trackedData>
                <li>Type of day (workday/weekend/vacation/sick)</li>
              </x-slot>
              <x-slot:category>Work</x-slot>
            </x-marketing.module>
          </div>

          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Work') &&
              (searchQuery === '' ||
                'primary obligation'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="🎯" name="Primary Obligation">
              <x-slot:trackedData>
                <li>What demanded most attention</li>
              </x-slot>
              <x-slot:category>Work</x-slot>
            </x-marketing.module>
          </div>

          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Body & Health') &&
              (searchQuery === '' ||
                'physical activity'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="🏃" name="Physical Activity">
              <x-slot:trackedData>
                <li>Did physical activity?</li>
                <li>Activity type</li>
                <li>Activity intensity</li>
              </x-slot>
              <x-slot:category>Body & Health</x-slot>
            </x-marketing.module>
          </div>
        </div>

        <!-- third row -->
        <div class="space-y-4">
          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Body & Health') &&
              (searchQuery === '' || 'health'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="🏥" name="Health">
              <x-slot:trackedData>
                <li>Health status</li>
              </x-slot>
              <x-slot:category>Body & Health</x-slot>
            </x-marketing.module>
          </div>

          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Body & Health') &&
              (searchQuery === '' || 'hygiene'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="🧼" name="Hygiene">
              <x-slot:trackedData>
                <li>Showered?</li>
                <li>Brushed teeth?</li>
                <li>Skincare?</li>
              </x-slot>
              <x-slot:category>Body & Health</x-slot>
            </x-marketing.module>
          </div>

          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Social') &&
              (searchQuery === '' || 'sexual activity'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="❤️" name="Sexual Activity">
              <x-slot:trackedData>
                <li>Had sexual activity?</li>
                <li>Sexual activity type</li>
              </x-slot>
              <x-slot:category>Social</x-slot>
            </x-marketing.module>
          </div>

          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Mind & Emotions') &&
              (searchQuery === '' || 'cognitive load'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="🧠" name="Cognitive Load">
              <x-slot:trackedData>
                <li>Cognitive load level</li>
                <li>Primary source</li>
                <li>Load quality</li>
              </x-slot>
              <x-slot:category>Mind & Emotions</x-slot>
            </x-marketing.module>
          </div>

          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Social') &&
              (searchQuery === '' || 'social density'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="👥" name="Social Density">
              <x-slot:trackedData>
                <li>Social density level</li>
              </x-slot>
              <x-slot:category>Social</x-slot>
            </x-marketing.module>
          </div>

          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Social') &&
              (searchQuery === '' || 'social events'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="🤝" name="Social Events">
              <x-slot:trackedData>
                <li>Event type</li>
                <li>Tone</li>
                <li>Duration</li>
              </x-slot>
              <x-slot:category>Social</x-slot>
            </x-marketing.module>
          </div>

          <div x-show="
            (selectedCategory === 'All' || selectedCategory === 'Places') &&
              (searchQuery === '' || 'travel'.includes(searchQuery.toLowerCase()))
          " x-transition>
            <x-marketing.module emoji="✈️" name="Travel">
              <x-slot:trackedData>
                <li>Did you travel?</li>
                <li>Travel details</li>
                <li>Travel mode</li>
              </x-slot>
              <x-slot:category>Places</x-slot>
            </x-marketing.module>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-marketing-layout>
