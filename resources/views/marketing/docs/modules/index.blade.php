<?php
/**
 * No view data.
 */
?>

<x-marketing-docs-layout>
  <div class="grid grid-cols-1 gap-x-16 lg:grid-cols-[1fr_250px]">
    <div class="py-16 sm:border-r sm:border-gray-200 sm:pr-10 dark:sm:border-gray-700">
      <x-marketing.docs.h1 title="Modules" />

      <p class="mb-8">{{ config('app.name') }} lets users write journal entries. Each journal entry follows a layout, which itself is made of modules. Modules are sets of fields that can be used to capture specific data.</p>

      <x-marketing.docs.h2 id="categories" title="Categories" />

      <p class="mb-2">{{ config('app.name') }} organizes modules into five core categories. This structure is intentional and opinionated. Categories are not cosmetic: they define how users think about their day, how data can be correlated, and how the system remains understandable as it grows.</p>

      <p class="mb-2">{{ config('app.name') }} deliberately limits itself to five categories, so the app is simple and understandable.</p>

      <p class="mb-2">Every module must clearly answer the question: <em>Which part of my life does this describe?</em>. If a module does not fit cleanly into one of these five, it likely does not belong in {{ config('app.name') }}.</p>

      <x-marketing.docs.h2 id="category-overview" title="Category overview" />

      <h3 class="font-semibold text-xl dark:text-gray-100 mb-3">💪 Body & Health</h3>
      <h4 class=" text-lg">What it represents</h4>
      <p class="mb-2">The physical state of the user and the biological constraints that shape the day.</p>
      <h4 class="font-semibold text-xl">What it brings</h4>
      <p class="mb-2">This category captures signals that strongly influence everything else but are often invisible in hindsight: fatigue, recovery, illness, physical strain. It provides grounding context for mood, performance, and decisions.</p>

      <h3 class="font-semibold text-xl dark:text-gray-100 mb-3">🧠 Mind & Emotions</h3>
      <h4 class=" text-lg">What it represents</h4>
      <p class="mb-2">The internal, subjective experience of the day: mood, stress, attention, and mental bandwidth.</p>
      <h4 class="font-semibold text-xl">What it brings</h4>
      <p class="mb-2">It explains how the day was lived, not just what happened. Capturing mental and emotional state makes patterns visible over time and clarifies limits, triggers, and recovery needs.</p>

      <h3 class="font-semibold text-xl dark:text-gray-100 mb-3">💼 Work</h3>
      <h4 class=" text-lg">What it represents</h4>
      <p class="mb-2">Structured effort and obligations: what you had to do, what you chose to do, and what you produced.</p>
      <h4 class="font-semibold text-xl">What it brings</h4>
      <p class="mb-2">It separates progress from noise. Work tracking in JournalOS focuses on leverage, friction, control, and trajectory so you can understand whether your time compounds or gets consumed by busywork.</p>

      <h3 class="font-semibold text-xl dark:text-gray-100 mb-3">👥 Social</h3>
      <h4 class=" text-lg">What it represents</h4>
      <p class="mb-2">Human interactions and relational dynamics: who you were around and how those interactions affected you.</p>
      <h4 class="font-semibold text-xl">What it brings</h4>
      <p class="mb-2">Social context is often the hidden driver behind mood, energy, and decisions. This category makes relationship patterns visible without turning JournalOS into a social network or a heavy CRM.</p>

      <h3 class="font-semibold text-xl dark:text-gray-100 mb-3">📍 Places</h3>
      <h4 class=" text-lg">What it represents</h4>
      <p class="mb-2">Environmental and spatial context: where you were and the conditions around you.</p>
      <h4 class="font-semibold text-xl">What it brings</h4>
      <p class="mb-2">Place acts as a silent modifier of behavior and wellbeing. Logging environment provides strong explanatory context when reviewing periods, especially for routines, travel, and seasonal effects.</p>
    </div>

    <!-- Sidebar -->
    <div class="mb-10 hidden w-full flex-shrink-0 flex-col justify-self-end lg:flex pt-16">
      <x-marketing.written-by-regis />

      <div class="flex flex-grow items-end">
        <div class="sticky bottom-0 w-full">
          <!-- Table of Contents -->
          <div class="mb-4">
            <h4 class="mb-2 text-xs font-semibold text-gray-500 uppercase">Jump to</h4>
            <nav class="space-y-1 text-sm">
              <a href="#countries" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white dark:text-gray-400 dark:hover:border-gray-600 dark:hover:bg-gray-800">Countries</a>
              <a href="#offices" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white dark:text-gray-400 dark:hover:border-gray-600 dark:hover:bg-gray-800">Offices</a>
              <a href="#divisions" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white dark:text-gray-400 dark:hover:border-gray-600 dark:hover:bg-gray-800">Divisions</a>
              <a href="#departments" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white dark:text-gray-400 dark:hover:border-gray-600 dark:hover:bg-gray-800">Departments</a>
              <a href="#teams" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white dark:text-gray-400 dark:hover:border-gray-600 dark:hover:bg-gray-800">Teams</a>
              <a href="#guilds" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white dark:text-gray-400 dark:hover:border-gray-600 dark:hover:bg-gray-800">Guilds</a>
              <a href="#key-principles" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white dark:text-gray-400 dark:hover:border-gray-600 dark:hover:bg-gray-800">Key principles</a>
              <a href="#headquarters-vs-branch-offices" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white dark:text-gray-400 dark:hover:border-gray-600 dark:hover:bg-gray-800">Headquarters vs. branch offices</a>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
