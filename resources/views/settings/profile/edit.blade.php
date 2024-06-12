<x-app-layout>
  <x-slot name="breadcrumb">
    <ul class="text-sm">
      <li class="inline after:content-['>'] after:text-gray-500 after:text-xs">
        <x-link href="{{ route('dashboard.index') }}">{{ __('Dashboard') }}</x-link>
      </li>
      <li class="inline">{{ __('Profile information') }}</li>
    </ul>
  </x-slot>

  <div class="mx-auto max-w-7xl px-4 sm:px-0">
    <div class="settings-grid grid grid-cols-1 gap-6">
      <!-- left -->
      <div>
        @include('settings.partials.sidebar')
      </div>

      <!-- right -->
      <div class="p-0 sm:px-0 sm:py-0  mb-6">
        <div class="max-w-7xl mx-auto sm:px-0 space-y-6">
          <div class="p-4 sm:p-4 bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="max-w-xl">
              @include('settings.profile.partials.update-profile-information-form')
            </div>
          </div>

          <div class="p-4 sm:p-4 bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="max-w-xl">
              @include('settings.profile.partials.update-password-form')
            </div>
          </div>

          <div class="p-4 sm:p-4 bg-white dark:bg-gray-800 shadow rounded-lg">
            <div class="max-w-xl">
              @include('settings.profile.partials.delete-user-form')
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
