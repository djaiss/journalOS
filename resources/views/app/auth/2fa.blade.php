<?php
/**
 * @var \Illuminate\Support\ViewErrorBag $errors
 */
?>

<x-guest-layout>
  <div class="grid min-h-screen w-screen grid-cols-1 lg:grid-cols-2">
    <!-- Left side - 2FA form -->
    <div class="mx-auto flex w-full max-w-2xl flex-1 flex-col justify-center px-5 py-10 sm:px-30">
      <div class="w-full">
        @if (config('app.show_marketing_site'))
          <p class="group mb-10 flex items-center gap-x-1 text-sm text-gray-600">
            <x-phosphor-arrow-left class="h-4 w-4 transition-transform duration-150 group-hover:-translate-x-1" />
            <x-link href="{{ route('marketing.index') }}" class="group-hover:underline">{{ __('Back to the marketing website') }}</x-link>
          </p>
        @endif

        <div class="mb-8 flex items-center gap-x-2">
          <a href="/" class="group flex items-center gap-x-2 transition-transform ease-in-out">
            <div class="flex h-7 w-7 items-center justify-center transition-all duration-400 group-hover:-translate-y-0.5 group-hover:-rotate-3">
              <x-image src="{{ asset('logo/30x30.webp') }}" srcset="{{ asset('logo/30x30.webp') }} 1x, {{ asset('logo/30x30@2x.webp') }} 2x" width="25" height="25" alt="JournalOS logo" />
            </div>
          </a>
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
            {{ __('Two-factor authentication') }}
          </h1>
        </div>

        <x-box class="mt-6 mb-12">
          <form method="POST" action="{{ route('2fa.challenge') }}">
            @csrf
            <div>
              <x-input type="text" id="code" value="{{ old('code') }}" :label="__('Enter your 2FA code')" required :error="$errors->get('code')" :passManagerDisabled="false" autocomplete="one-time-code" autofocus />
            </div>
            <div class="mt-6 flex items-center justify-between">
              <x-button>{{ __('Verify') }}</x-button>
            </div>
          </form>
        </x-box>

        <!-- Register link -->
        <x-box class="mb-8 text-center text-sm">
          {{ __('New to :organization?', ['organization' => config('app.name')]) }}
          <x-link :href="route('register')" class="ml-1">
            {{ __('Create an account') }}
          </x-link>
        </x-box>

        <ul class="text-xs text-gray-600">
          <li>© {{ config('app.name') }} {{ now()->format('Y') }}</li>
        </ul>
      </div>
    </div>
    <!-- Right side - Illustration (optional, blank for now) -->
    <div class="login-image relative hidden lg:block">
      <!-- You can add a 2FA-related illustration here if desired -->
    </div>
  </div>
</x-guest-layout>
