<x-app-layout>
  <x-slot name="breadcrumb">
    <ul class="text-sm">
      <li class="inline after:content-['>'] after:text-gray-500 after:text-xs">
        <x-link href="{{ route('dashboard.index') }}">{{ __('Dashboard') }}</x-link>
      </li>
      <li class="inline">{{ __('API Access') }}</li>
    </ul>
  </x-slot>

  <div class="mx-auto max-w-7xl px-4 sm:px-0">
    <div class="settings-grid grid grid-cols-1 gap-6">
      <!-- left -->
      <div>
        @include('settings.partials.sidebar')
      </div>

      <!-- right -->
      <div class="p-0 sm:px-0 sm:py-0">
        <div class="max-w-7xl mx-auto sm:px-0 space-y-6">
          <div class="p-4 sm:p-4 bg-white dark:bg-gray-800 shadow rounded-lg">
            <header class="mb-4">
              <div class="flex items-center justify-between">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                  {{ __('API acccess keys') }}
                </h2>

                @if (count($data['tokens']))
                <x-primary-link dusk="main-cta-add-key" href="{{ route('settings.api.new') }}" class="text-sm">
                  <x-heroicon-c-plus class="w-4 h-4 mr-1" />
                  <span>{{ __('Add key') }}</span>
                </x-primary-link>
                @endif
              </div>

              <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('This is a list of all the keys that let you access your data, and all the data from organizations you belong to, through the API.') }}
              </p>
            </header>

            <!-- new key added, only displayed once -->
            @if (session('key'))
            <div class="bg-green-50 dark:bg-green-900 border-l-2 border-green-300 dark:border-green-500 p-4 rounded-lg mb-3">
              <div>
                <div class="text-sm mb-2">{{ __('This is the key you just added. Make sure to copy it now, you won\'t be able to see it again.') }}</div>
                <div class="font-mono border bg-white rounded px-2 py-1 text-sm">
                  {{ session('key') }}
                </div>
              </div>
            </div>
            @endif

            <div hx-target="#tokens-index" hx-swap="innerHTML" hx-get="{{ route('settings.api.index') }}" hx-trigger="loadTokens from:body">
              @include('settings.api.partials.tokens-list')
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
