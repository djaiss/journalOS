<?php

/**
 * No view data.
 */
?>

<x-marketing-docs-layout>
  <div class="grid grid-cols-1 gap-x-16 lg:grid-cols-[1fr_250px]">
    <div class="py-16 sm:border-r sm:border-gray-200 sm:pr-10 dark:sm:border-gray-700">
      <x-marketing.docs.h1 title="Modules" />

      <p class="mb-8 text-gray-700 dark:text-gray-300">{{ config('app.name') }} lets users write journal entries. Each journal entry follows a layout, which itself is made of modules. Modules are sets of fields that can be used to capture specific data.</p>

      <x-marketing.docs.h2 id="categories" title="Categories" />

      <p class="mb-2 text-gray-700 dark:text-gray-300">{{ config('app.name') }} organizes modules into five core categories. We don't want to have more categories - it forces us to think about the different aspect of our lives in a simpler manner.</p>

      <p class="mb-8 text-gray-700 dark:text-gray-300">
        Every module must clearly answer the question:
        <em>Which part of my life does this describe?</em>
        . If a module does not fit cleanly into one of these five, it likely does not belong in {{ config('app.name') }}.
      </p>

      <x-marketing.docs.h2 id="category-overview" title="Category overview" />

      <h3 class="mt-6 mb-4 rounded-lg border border-gray-300 p-2 text-xl font-semibold text-gray-900 dark:text-gray-100">💪 Body & Health</h3>
      <h4 class="mb-2 font-semibold text-gray-900 dark:text-gray-100">What it represents</h4>
      <p class="mb-3 text-gray-700 dark:text-gray-300">The physical state of the user and the biological constraints that shape the day.</p>
      <h4 class="mb-2 font-semibold text-gray-900 dark:text-gray-100">What it brings</h4>
      <p class="mb-8 text-gray-700 dark:text-gray-300">This category captures signals that strongly influence everything else but are often invisible in hindsight: fatigue, recovery, illness, physical strain. It provides grounding context for mood, performance, and decisions.</p>

      <h3 class="mt-6 mb-4 rounded-lg border border-gray-300 p-2 text-xl font-semibold text-gray-900 dark:text-gray-100">🧠 Mind & Emotions</h3>
      <h4 class="mb-2 font-semibold text-gray-900 dark:text-gray-100">What it represents</h4>
      <p class="mb-3 text-gray-700 dark:text-gray-300">The internal, subjective experience of the day: mood, stress, attention, and mental bandwidth.</p>
      <h4 class="mb-2 font-semibold text-gray-900 dark:text-gray-100">What it brings</h4>
      <p class="mb-8 text-gray-700 dark:text-gray-300">It explains how the day was lived, not just what happened. Capturing mental and emotional state makes patterns visible over time and clarifies limits, triggers, and recovery needs.</p>

      <h3 class="mt-6 mb-4 rounded-lg border border-gray-300 p-2 text-xl font-semibold text-gray-900 dark:text-gray-100">💼 Work</h3>
      <h4 class="mb-2 font-semibold text-gray-900 dark:text-gray-100">What it represents</h4>
      <p class="mb-3 text-gray-700 dark:text-gray-300">Structured effort and obligations: what you had to do, what you chose to do, and what you produced.</p>
      <h4 class="mb-2 font-semibold text-gray-900 dark:text-gray-100">What it brings</h4>
      <p class="mb-8 text-gray-700 dark:text-gray-300">It separates progress from noise. Work tracking in JournalOS focuses on leverage, friction, control, and trajectory so you can understand whether your time compounds or gets consumed by busywork.</p>

      <h3 class="mt-6 mb-4 rounded-lg border border-gray-300 p-2 text-xl font-semibold text-gray-900 dark:text-gray-100">👥 Social</h3>
      <h4 class="mb-2 font-semibold text-gray-900 dark:text-gray-100">What it represents</h4>
      <p class="mb-3 text-gray-700 dark:text-gray-300">Human interactions and relational dynamics: who you were around and how those interactions affected you.</p>
      <h4 class="mb-2 font-semibold text-gray-900 dark:text-gray-100">What it brings</h4>
      <p class="mb-8 text-gray-700 dark:text-gray-300">Social context is often the hidden driver behind mood, energy, and decisions. This category makes relationship patterns visible without turning JournalOS into a social network or a heavy CRM.</p>

      <h3 class="mt-6 mb-4 rounded-lg border border-gray-300 p-2 text-xl font-semibold text-gray-900 dark:text-gray-100">📍 Places</h3>
      <h4 class="mb-2 font-semibold text-gray-900 dark:text-gray-100">What it represents</h4>
      <p class="mb-3 text-gray-700 dark:text-gray-300">Environmental and spatial context: where you were and the conditions around you.</p>
      <h4 class="mb-2 font-semibold text-gray-900 dark:text-gray-100">What it brings</h4>
      <p class="mb-6 text-gray-700 dark:text-gray-300">Place acts as a silent modifier of behavior and wellbeing. Logging environment provides strong explanatory context when reviewing periods, especially for routines, travel, and seasonal effects.</p>
    </div>

    <!-- Sidebar -->
    <div class="mb-10 hidden w-full flex-shrink-0 flex-col justify-self-end pt-16 lg:flex">
      <x-marketing.written-by-regis />

      <div class="flex flex-grow items-end">
        <div class="sticky bottom-0 w-full">
          <!-- Table of Contents -->
          <div class="mb-4">
            <h4 class="mb-2 text-xs font-semibold text-gray-500 uppercase dark:text-gray-400">Jump to</h4>
            <nav class="space-y-1 text-sm">
              <a href="#categories" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white dark:text-gray-400 dark:hover:border-gray-600 dark:hover:bg-gray-800">Categories</a>
              <a href="#category-overview" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white dark:text-gray-400 dark:hover:border-gray-600 dark:hover:bg-gray-800">Category overview</a>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
