<x-app-layout>
  <x-slot name="breadcrumb">
    <ul class="text-sm">
      <li class="inline after:content-['>'] after:text-gray-500 after:text-xs">
        <x-link href="{{ route('dashboard.index') }}">{{ __('Dashboard') }}</x-link>
      </li>
      <li class="inline after:content-['>'] after:text-gray-500 after:text-xs">
        <x-link href="{{ route('settings.api.index') }}">{{ __('API Access') }}</x-link>
      </li>
      <li class="inline">{{ __('New key') }}</li>
    </ul>
  </x-slot>

  <div class="mx-auto max-w-2xl px-4 sm:px-0">
    <div class="p-4 sm:p-4 bg-white dark:bg-gray-800 shadow rounded-lg">
      <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
          {{ __('Add a new API key to your account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          {{ __('This will let you access all your data through the API, in a programmatic way.') }}
        </p>
      </header>

      <form method="post" action="{{ route('settings.api.create') }}" class="mt-6 space-y-4">
        @csrf
        @method('post')

        <div>
          <x-input-label for="token_name" :value="__('Give this key a descriptive name')" />
          <x-text-input id="token_name" name="token_name" type="text" class="mt-1 block w-full" autofocus required />
          <x-input-error :messages="$errors->get('token_name')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
          <x-primary-button dusk="cta-create">{{ __('Create') }}</x-primary-button>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>
