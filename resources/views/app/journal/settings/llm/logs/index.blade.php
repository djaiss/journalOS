<?php

/**
 * @var \App\Models\Journal $journal
 * @var \Illuminate\Pagination\CursorPaginator $accessLogs
 */
?>

<x-app-layout :journal="$journal">
  <x-slot:title>
    {{ __('LLM access logs') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => $journal->name, 'route' => route('journal.show', ['slug' => $journal->slug]) ],
    ['label' => __('Settings'), 'route' => route('journal.settings.modules.index', ['slug' => $journal->slug])],
    ['label' => __('LLM access'), 'route' => route('journal.settings.llm.index', ['slug' => $journal->slug])],
    ['label' => __('Access history')]
  ]" />

  <div class="grid grow bg-gray-50 sm:grid-cols-[220px_1fr] dark:bg-gray-950">
    @include('app.journal.settings.partials.sidebar', ['journal' => $journal])

    <section class="p-4 sm:p-8">
      <div class="mx-auto max-w-4xl sm:px-0">
        <x-box id="llm-access-logs" x-merge="append" padding="p-0">
          <x-slot:title>{{ __('Access history') }}</x-slot>

          @forelse ($accessLogs as $log)
            @php
              $requestedDate = sprintf('%04d', $log->requested_year);
              if ($log->requested_month !== null) {
                $requestedDate .= '-' . sprintf('%02d', $log->requested_month);
              }
              if ($log->requested_day !== null) {
                $requestedDate .= '-' . sprintf('%02d', $log->requested_day);
              }
            @endphp

            <div class="flex items-center justify-between border-b border-gray-200 p-3 text-sm first:rounded-t-lg last:rounded-b-lg last:border-b-0 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
              <div class="flex items-center gap-3">
                <x-phosphor-pulse class="size-3 min-w-3 text-zinc-600 dark:text-zinc-400" />
                <div class="flex flex-col gap-y-2">
                  <p class="font-mono text-xs text-zinc-500 dark:text-zinc-400">{{ $requestedDate }}</p>
                  <p class="font-mono text-xs break-all">{{ $log->request_url }}</p>
                </div>
              </div>

              <x-tooltip text="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                <p class="font-mono text-xs">{{ $log->created_at->diffForHumans() }}</p>
              </x-tooltip>
            </div>
          @empty
            <p class="p-3 text-sm text-gray-600 dark:text-gray-300">{{ __('No LLM accesses yet.') }}</p>
          @endforelse

          @if ($accessLogs->nextPageUrl())
            <div id="pagination" class="flex justify-center rounded-b-lg p-3 text-sm hover:bg-blue-50 dark:hover:bg-gray-800">
              <x-link x-target="llm-access-logs pagination" href="{{ $accessLogs->nextPageUrl() }}" class="text-center">{{ __('Load more') }}</x-link>
            </div>
          @endif
        </x-box>
      </div>
    </section>
  </div>
</x-app-layout>
