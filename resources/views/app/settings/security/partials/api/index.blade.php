<?php
/*
 * @var \Illuminate\Support\Collection $apiKeys
 */
?>

<x-box class="mb-6" padding="p-0">
  <x-slot:title>{{ __('Personal API Keys') }}</x-slot>
  <x-slot:description>
    <p>{{ __('API keys are like secret passwords that allow other tools or apps to connect securely to your account.') }}</p>
    <p>{{ __('Each API key is unique to you. Treat them like private passwords—don’t share them with anyone you don’t trust.') }}</p>
  </x-slot>

  <div id="api-key-notification">
    @if (session('apiKey'))
      <div class="m-4 rounded-lg border border-green-300 bg-green-50 p-4">
        <div class="flex items-start">
          <div class="flex-shrink-0">
            <x-phosphor-check-circle class="h-5 w-5 text-green-500" />
          </div>

          <div class="ml-3 w-full">
            <h3 class="text-sm font-medium text-green-800">{{ __('API Key created successfully') }}</h3>

            <div class="mt-2">
              <div class="text-sm text-green-700">
                {{ __('Please copy your API key now. For security reasons, it won\'t be shown again.') }}
              </div>

              <div class="mt-3" x-data="{
                copied: false,
                copyToClipboard() {
                  const el = document.createElement('textarea')
                  el.value = '{{ session('apiKey') }}'
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
                <div class="flex items-center gap-x-2">
                  <code class="flex-1 rounded border-gray-50 bg-white px-3 py-2 text-center font-mono text-sm text-gray-800">{{ session('apiKey') }}</code>
                  <button @click="copyToClipboard()" class="inline-flex items-center rounded-md border border-green-200 bg-white px-3 py-2 text-sm font-semibold text-green-600 shadow-sm hover:bg-green-50 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 focus:outline-none">
                    <x-phosphor-check x-show="copied" class="mr-1 h-4 w-4" />
                    <x-phosphor-copy x-show="!copied" class="mr-1 h-4 w-4" />
                    <span x-text="copied ? '{{ __('Copied') }}' : '{{ __('Copy') }}'"></span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
  </div>

  <div id="new-api-key-form" class="flex items-center justify-between rounded-t-lg p-3 last:rounded-b-lg last:border-b-0 hover:bg-blue-50">
    @if ($apiKeys->isEmpty())
      <p class="text-sm text-zinc-500">{{ __('No API keys created') }}</p>
    @else
      <p class="text-sm text-zinc-500">{{ __(':count API key(s) created', ['count' => $apiKeys->count()]) }}</p>
    @endif

    <x-button.secondary href="{{ route('settings.api-keys.create') }}" x-target="new-api-key-form" class="mr-2 text-sm" data-test="new-api-key-button">
      {{ __('New API key') }}
    </x-button.secondary>
  </div>

  @if (! $apiKeys->isEmpty())
    <div id="api-key-list">
      @foreach ($apiKeys as $apiKey)
        <div class="group flex items-center justify-between border-b border-gray-200 p-3 first:border-t last:rounded-b-lg last:border-b-0">
          <div class="flex items-center justify-between gap-3">
            <div class="rounded-sm bg-zinc-100 p-2">
              <x-phosphor-key class="h-4 w-4 text-zinc-500" />
            </div>

            <div class="flex flex-col">
              <p class="text-sm font-semibold">{{ $apiKey->name }}</p>
              <p class="font-mono text-xs text-zinc-500">{{ $apiKey->last_used }}</p>
            </div>
          </div>

          <x-form
            x-target="api-key-list"
            action="{{ route('settings.api-keys.destroy', $apiKey->id) }}"
            method="delete"
            x-on:ajax:before="
            confirm('Are you sure you want to proceed? This can not be undone.') ||
              $event.preventDefault()
          ">
            <x-button x-target="api-key-list" class="text-sm" data-test="delete-api-key-{{ $apiKey->id }}">
              {{ __('Delete') }}
            </x-button>
          </x-form>
        </div>
      @endforeach
    </div>
  @endif
</x-box>
