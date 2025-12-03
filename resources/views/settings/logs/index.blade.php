<?php
/*
 * @var $logs
 */
?>

<x-app-layout>
  <x-slot:title>
    {{ __('Profile') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('organization.index')],
    ['label' => __('Settings'), 'route' => route('settings.profile.index')],
    ['label' => __('Logs')]
  ]" />

  <!-- settings layout -->
  <div class="grid flex-grow sm:grid-cols-[220px_1fr]">
    <!-- Sidebar -->
    @include('settings.partials.sidebar')

    <!-- Main content -->
    <section class="p-4 sm:p-8">
      <div class="mx-auto max-w-4xl sm:px-0">
        <x-box id="logs-container" x-merge="append" padding="p-0">
          <x-slot:title>{{ __('Logs') }}</x-slot>
          <!-- last actions -->
          @foreach ($logs as $log)
            <div class="flex items-center justify-between border-b border-gray-200 p-3 text-sm first:rounded-t-lg last:rounded-b-lg last:border-b-0 hover:bg-blue-50">
              <div class="flex items-center gap-3">
                <x-phosphor-pulse class="size-3 min-w-3 text-zinc-600 dark:text-zinc-400" />
                <div class="flex flex-col gap-y-2">
                  <p class="flex items-center gap-2">
                    <span class="">{{ $log->getUserName() }}</span>
                    |
                    @if ($log->organization)
                      <x-link href="{{ route('organization.show', $log->organization->id) }}">{{ $log->organization->name }}</x-link>
                      |
                    @endif

                    <span class="font-mono text-xs">{{ $log->action }}</span>
                  </p>
                  <p class="">{{ $log->description }}</p>
                </div>
              </div>

              <x-tooltip text="{{ $log->created_at->format('Y-m-d H:i:s') }}">
                <p class="font-mono text-xs">{{ $log->created_at->diffForHumans() }}</p>
              </x-tooltip>
            </div>
          @endforeach

          @if ($logs->nextPageUrl())
            <div id="pagination" class="flex justify-center rounded-b-lg p-3 text-sm hover:bg-blue-50">
              <x-link x-target="logs-container pagination" href="{{ $logs->nextPageUrl() }}" class="text-center">{{ __('Load more') }}</x-link>
            </div>
          @endif
        </x-box>
      </div>
    </section>
  </div>
</x-app-layout>
