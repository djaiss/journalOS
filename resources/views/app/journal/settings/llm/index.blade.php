<?php

/**
 * @var \App\Models\Journal $journal
 * @var \Illuminate\Support\Collection $accessLogs
 * @var bool $hasMoreAccessLogs
 * @var \Illuminate\Support\ViewErrorBag $errors
 */
?>

<x-app-layout :journal="$journal">
  <x-slot:title>
    {{ __('LLM access') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => $journal->name, 'route' => route('journal.show', ['slug' => $journal->slug]) ],
    ['label' => __('Settings'), 'route' => route('journal.settings.modules.index', ['slug' => $journal->slug])],
    ['label' => __('LLM access')]
  ]" />

  <div class="grid grow bg-gray-50 sm:grid-cols-[220px_1fr] dark:bg-gray-950">
    @include('app.journal.settings.partials.sidebar', ['journal' => $journal])

    <section class="p-4 sm:p-8">
      <div id="llm-content" class="mx-auto flex max-w-2xl flex-col gap-y-8 sm:px-0">
        <x-box padding="p-0">
          <x-slot:title>
            {{ __('LLM access') }}
          </x-slot>

          <x-slot:description>
            {{ __('You can access your account with an LLM like ChatGPT or Claude. Basically, if you activate it, we will give you a link that will let you query a specific day or a month and get in return an LLM-friendly response. With that you will be able to get stats or whatever you want with it. This URL is quite secure, but we strongly suggest that you keep it private and be very careful with it as it can access, in read mode, all your data.') }}
          </x-slot>

          <x-form method="put" x-target="llm-access-form llm-access-key notifications" x-target.back="llm-access-form llm-access-key" id="llm-access-form" :action="route('journal.settings.llm.update', ['slug' => $journal->slug])">
            <div class="grid grid-cols-3 items-center rounded-t-lg border-b border-gray-200 p-3 last:rounded-b-lg hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
              <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Allow LLM access') }}</p>
              <div class="col-span-1 w-full justify-self-end">
                <x-select id="has_llm_access" name="has_llm_access" :options="[
                  1 => __('Yes'),
                  0 => __('No'),
                ]" :selected="old('has_llm_access', $journal->has_llm_access ? 1 : 0)" required :error="$errors->get('has_llm_access')" />
              </div>
            </div>

            <!-- actions -->
            <div class="flex items-center justify-end p-3">
              <x-button>{{ __('Save') }}</x-button>
            </div>
          </x-form>
        </x-box>

        <div id="llm-access-key">
          @if ($journal->has_llm_access && $journal->llm_access_key)
            <x-box padding="p-2">
              <x-slot:title>
                {{ __('Access key') }}
              </x-slot>

              <x-slot:description>
                {{ __('Keep this key private. Anyone with it can access your data in read-only mode.') }}
              </x-slot>

              <div class="flex items-center gap-x-2" x-data="{
                copied: false,
                copyToClipboard() {
                  const el = document.createElement('textarea')
                  el.value = '{{ $journal->llm_access_key }}'
                  document.body.appendChild(el)
                  el.select()
                  document.execCommand('copy')
                  document.body.removeChild(el)

                  this.copied = true
                  setTimeout(() => {
                    this.copied = false
                  }, 2000)
                },
              }">
                <code class="flex-1 break-all">{{ $journal->llm_access_key }}</code>
                <button @click="copyToClipboard()" class="inline-flex items-center rounded-md border border-green-200 bg-white px-3 py-2 text-sm font-semibold text-green-600 shadow-sm hover:bg-green-50 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:outline-none dark:border-green-700 dark:bg-gray-900 dark:text-green-300 dark:hover:bg-gray-800">
                  <x-phosphor-check x-show="copied" class="mr-1 h-4 w-4" />
                  <x-phosphor-copy x-show="!copied" class="mr-1 h-4 w-4" />
                  <span x-text="copied ? '{{ __('Copied') }}' : '{{ __('Copy') }}'"></span>
                </button>
              </div>
            </x-box>
          @endif
        </div>

        <div class="mt-4">
          <x-box padding="p-0">
            <x-slot:title>
              {{ __('Access history') }}
            </x-slot>

            <x-slot:description>
              {{ __('We keep track of each LLM access to help you monitor usage.') }}
            </x-slot>

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

            @if ($hasMoreAccessLogs)
              <div class="flex justify-center rounded-b-lg p-3 text-sm">
                <x-link href="{{ route('journal.settings.llm.logs.index', ['slug' => $journal->slug]) }}" class="text-center">{{ __('View more') }}</x-link>
              </div>
            @endif
          </x-box>
        </div>
      </div>
    </section>
  </div>
</x-app-layout>
