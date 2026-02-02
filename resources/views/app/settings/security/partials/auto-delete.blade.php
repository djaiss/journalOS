<?php

/**
 * @var \Illuminate\Support\ViewErrorBag $errors
 */
?>

<x-box padding="p-0">
  <x-slot:title>
    {{ __('Auto delete account') }}
  </x-slot>

  <x-slot:description>
    {{ __('Automatically delete the account and all the data after 6 months of inactivity. Please be certain.') }}
  </x-slot>

  <x-form id="auto-delete-account-form" x-target="auto-delete-account-form" x-target.back="auto-delete-account-form" action="{{ route('settings.security.auto-delete.update') }}" method="put">
    <div class="grid grid-cols-3 items-center rounded-t-lg p-3 last:rounded-b-lg hover:bg-blue-50 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Delete my account after 6 months of inactivity') }}</p>
      <div class="col-span-1 w-full justify-self-end">
        <x-select id="auto_delete_account" :options="[
          'yes' => __('Yes'),
          'no' => __('No'),
        ]" selected="{{ old('auto_delete_account', auth()->user()->auto_delete_account ? 'yes' : 'no') }}" :error="$errors->get('auto_delete_account')" />
      </div>
    </div>

    <!-- actions -->
    <div class="flex justify-end border-t border-gray-200 p-3 dark:border-gray-700">
      <x-button>
        {{ __('Save') }}
      </x-button>
    </div>
  </x-form>
</x-box>
