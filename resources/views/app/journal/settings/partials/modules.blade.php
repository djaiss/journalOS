<?php
/**
 * @var \App\Models\Journal $journal
 */
?>

<div class="flex flex-col gap-4" x-data="{ activeTab: 'all' }">
  <h2 class="font-semi-bold mb-1 text-lg">{{ __('Modules') }}</h2>
  <div class="mb-2 flex flex-col gap-y-2 text-sm text-gray-500 dark:text-gray-400">
    {{ __('Manage the modules available for this journal. Disabling a module will not delete its data. It will only hide the module from the journal.') }}
  </div>

  <!-- Tabs Navigation -->
  <div>
    <div class="inline-flex h-9 items-center justify-start gap-1 rounded-lg bg-gray-100 p-1 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
      <button type="button" @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-white text-gray-900 shadow dark:bg-gray-950 dark:text-gray-50' : ''" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-md px-3 py-1 text-sm font-medium whitespace-nowrap ring-offset-white transition-all hover:bg-white hover:shadow focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 dark:ring-offset-gray-950 hover:dark:bg-gray-950 hover:dark:text-gray-50 dark:focus-visible:ring-gray-300">
        <span>{{ __('All') }}</span>
        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs dark:bg-gray-700">14</span>
      </button>
      <button type="button" @click="activeTab = 'body'" :class="activeTab === 'body' ? 'bg-white text-gray-900 shadow dark:bg-gray-950 dark:text-gray-50' : ''" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-md px-3 py-1 text-sm font-medium whitespace-nowrap ring-offset-white transition-all hover:bg-white hover:shadow focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 dark:ring-offset-gray-950 hover:dark:bg-gray-950 hover:dark:text-gray-50 dark:focus-visible:ring-gray-300">
        <span>💪</span>
        <span>{{ __('Body & Health') }}</span>
        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs dark:bg-gray-700">4</span>
      </button>
      <button type="button" @click="activeTab = 'mind'" :class="activeTab === 'mind' ? 'bg-white text-gray-900 shadow dark:bg-gray-950 dark:text-gray-50' : ''" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-md px-3 py-1 text-sm font-medium whitespace-nowrap ring-offset-white transition-all hover:bg-white hover:shadow focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 dark:ring-offset-gray-950 hover:dark:bg-gray-950 hover:dark:text-gray-50 dark:focus-visible:ring-gray-300">
        <span>🧠</span>
        <span>{{ __('Mind & Emotions') }}</span>
        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs dark:bg-gray-700">2</span>
      </button>
      <button type="button" @click="activeTab = 'work'" :class="activeTab === 'work' ? 'bg-white text-gray-900 shadow dark:bg-gray-950 dark:text-gray-50' : ''" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-md px-3 py-1 text-sm font-medium whitespace-nowrap ring-offset-white transition-all hover:bg-white hover:shadow focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 dark:ring-offset-gray-950 hover:dark:bg-gray-950 hover:dark:text-gray-50 dark:focus-visible:ring-gray-300">
        <span>💼</span>
        <span>{{ __('Work') }}</span>
        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs dark:bg-gray-700">3</span>
      </button>
      <button type="button" @click="activeTab = 'social'" :class="activeTab === 'social' ? 'bg-white text-gray-900 shadow dark:bg-gray-950 dark:text-gray-50' : ''" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-md px-3 py-1 text-sm font-medium whitespace-nowrap ring-offset-white transition-all hover:bg-white hover:shadow focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 dark:ring-offset-gray-950 hover:dark:bg-gray-950 hover:dark:text-gray-50 dark:focus-visible:ring-gray-300">
        <span>👥</span>
        <span>{{ __('Social') }}</span>
        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs dark:bg-gray-700">3</span>
      </button>
      <button type="button" @click="activeTab = 'places'" :class="activeTab === 'places' ? 'bg-white text-gray-900 shadow dark:bg-gray-950 dark:text-gray-50' : ''" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-md px-3 py-1 text-sm font-medium whitespace-nowrap ring-offset-white transition-all hover:bg-white hover:shadow focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 dark:ring-offset-gray-950 hover:dark:bg-gray-950 hover:dark:text-gray-50 dark:focus-visible:ring-gray-300">
        <span>📍</span>
        <span>{{ __('Places') }}</span>
        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs dark:bg-gray-700">2</span>
      </button>
    </div>
  </div>
</div>
