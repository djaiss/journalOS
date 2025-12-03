<?php
/*
 * @var \App\Models\User $user
 */
?>

<x-box class="mb-6" padding="p-0">
  <x-slot:title>{{ __('Change password') }}</x-slot>
  <x-form method="put" action="{{ route('settings.password.update') }}">
    <!-- current password -->
    <div class="grid grid-cols-3 items-center rounded-t-lg border-b border-gray-200 p-3 hover:bg-blue-50">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Current password') }}</p>
      <div class="w-full justify-self-end">
        <x-input id="current_password" type="password" required :error="$errors->get('current_password')" autofocus />
      </div>
    </div>

    <!-- new password -->
    <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('New password') }}</p>
      <div class="w-full justify-self-end">
        <x-input id="new_password" type="password" help="{{ __('Minimum 8 characters.') }}" passwordrules="minlength: 8" required :error="$errors->get('new_password')" :passManagerDisabled="false" />
      </div>
    </div>

    <!-- confirm new password -->
    <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Confirm new password') }}</p>
      <div class="w-full justify-self-end">
        <x-input id="new_password_confirmation" type="password" name="new_password_confirmation" required :error="$errors->get('new_password_confirmation')" />
      </div>
    </div>

    <div class="flex items-center justify-end p-3">
      <x-button>{{ __('Save') }}</x-button>
    </div>
  </x-form>
</x-box>
