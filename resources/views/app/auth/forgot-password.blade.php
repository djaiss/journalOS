<?php
/**
 * No view data.
 */
?>

<x-guest-layout>
  <div class="grid min-h-screen w-screen grid-cols-1 lg:grid-cols-2">
    <!-- Left side - Forgot password form -->
    <div class="mx-auto flex w-full max-w-2xl flex-1 flex-col justify-center px-5 py-10 sm:px-30">
      <div class="w-full">
        @if (config('services.journalos.show_marketing_site'))
          <p class="group mb-10 flex items-center gap-x-1 text-sm text-gray-600">
            <x-phosphor-arrow-left class="h-4 w-4 transition-transform duration-150 group-hover:-translate-x-1" />
            <x-link href="{{ route('marketing.index') }}" class="group-hover:underline">{{ __('Back to the marketing website') }}</x-link>
          </p>
        @endif

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Title -->
        <div class="mb-8 flex items-center gap-x-2">
          <a href="" class="group flex items-center gap-x-2 transition-transform ease-in-out">
            <div class="flex h-7 w-7 items-center justify-center transition-all duration-400 group-hover:-translate-y-0.5 group-hover:-rotate-3">
              <x-image src="{{ asset('logo/30x30.webp') }}" srcset="{{ asset('logo/30x30.webp') }} 1x, {{ asset('logo/30x30@2x.webp') }} 2x" width="25" height="25" alt="JournalOS logo" />
            </div>
          </a>
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
            {{ __('Forgot password') }}
          </h1>
        </div>

        <!-- Forgot password form -->
        <x-box class="mb-8">
          <x-form method="post" action="{{ route('password.email') }}" class="space-y-4">
            <!-- Email Address -->
            <x-input id="email" type="email" :label="__('Email address')" required autofocus placeholder="john@doe.com" />

            <div class="flex items-center justify-between">
              <x-link href="{{ route('login') }}" class="text-sm text-gray-600">
                {{ __('Back to login') }}
              </x-link>

              <x-button>{{ __('Email password reset link') }}</x-button>
            </div>
          </x-form>
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

    <!-- Right side - Image -->
    <div class="relative hidden bg-gray-400 lg:block">
      <!-- Quote Box -->
      <div class="absolute inset-0 flex items-center justify-center">bla</div>
    </div>
  </div>
</x-guest-layout>
