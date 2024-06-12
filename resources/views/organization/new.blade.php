<x-app-layout>
  <x-slot name="breadcrumb">
    <ul class="text-sm">
      <li class="inline after:content-['>'] after:text-gray-500 after:text-xs">
        <x-link href="{{ route('dashboard.index') }}">{{ __('Dashboard') }}</x-link>
      </li>
      <li class="inline">{{ __('Create an organization') }}</li>
    </ul>
  </x-slot>

  <div class="mx-auto max-w-2xl px-4 sm:px-0">
    <div class="p-4 sm:p-4 bg-white dark:bg-gray-800 shadow rounded-lg">
      <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
          {{ __('Create a new organization') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
          {{ __('You will be the owner of this new organization.') }}
        </p>
      </header>

      <form method="post" action="{{ route('organization.create') }}" class="mt-6 space-y-4">
        @csrf
        @method('post')

        <div>
          <x-input-label for="organization_name" :value="__('Name of the organization')" />
          <x-text-input id="organization_name" name="organization_name" type="text" class="mt-1 block w-full" autofocus required />
          <x-input-error :messages="$errors->get('organization_name')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
          <x-primary-button dusk="cta-create">{{ __('Create') }}</x-primary-button>
        </div>
      </form>
    </div>
  </div>
</x-app-layout>
