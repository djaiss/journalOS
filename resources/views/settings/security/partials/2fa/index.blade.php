<?php
/*
 * @var Collection $apiKeys
 * @var bool $has2fa
 */
?>

@include('settings.security.partials.2fa.preferred-method')

<x-box padding="p-0" class="mb-6">
  <!-- Authenticator app -->
  <div id="authenticator-app" class="flex items-center rounded-t-lg border-b border-gray-200 p-3 hover:bg-blue-50">
    <x-phosphor-device-mobile class="h-5 w-5 text-gray-500" />
    <div class="ms-5 flex w-full items-center justify-between">
      <div>
        <p class="font-semibold">
          {{ __('Authenticator app') }}
          @if ($has2fa)
            <span class="me-3 rounded-sm bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800 dark:bg-green-900 dark:text-green-300">{{ __('Configured') }}</span>
          @endif
        </p>
        <p class="text-xs text-gray-600">{{ __('Use an authentication app to get two-factor authentication codes when prompted.') }}</p>
      </div>

      @if ($has2fa)
        <x-button.secondary href="{{ route('settings.security.2fa.create') }}" x-target="authenticator-app" class="mr-2 text-sm">
          {{ __('Edit') }}
        </x-button.secondary>
      @else
        <x-button.secondary href="{{ route('settings.security.2fa.create') }}" x-target="authenticator-app" class="mr-2 text-sm">
          {{ __('Set up') }}
        </x-button.secondary>
      @endif
    </div>
  </div>

  <!-- recovery codes -->
  @if ($has2fa)
    <div id="recovery-codes" class="flex items-center border-b border-gray-200 p-3 hover:bg-blue-50">
      <x-phosphor-toolbox class="h-5 w-5 text-gray-500" />
      <div class="ms-5 flex w-full items-center justify-between">
        <div>
          <p class="font-semibold">
            {{ __('Recovery codes') }}
          </p>
          <p class="text-xs text-gray-600">{{ __('Use these codes to access your account if you lose access to your authenticator app.') }}</p>
        </div>

        <x-button.secondary href="{{ route('settings.security.recoverycodes.show') }}" x-target="recovery-codes" class="mr-2 text-sm">
          {{ __('Show') }}
        </x-button.secondary>
      </div>
    </div>
  @endif

  <!-- Code by email -->
  <div class="flex items-center rounded-b-lg p-3 hover:bg-blue-50">
    <x-phosphor-envelope class="h-5 w-5 text-gray-500" />
    <div class="ms-5 flex w-full items-center justify-between">
      <div class="">
        <p class="font-semibold">{{ __('Code by email') }}</p>
        <p class="text-xs text-gray-600">{{ __('Receive a one-time code via email.') }}</p>
      </div>
    </div>
  </div>
</x-box>
