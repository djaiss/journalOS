<?php
/**
 * @var \Illuminate\Support\ViewErrorBag $errors
 */
?>

<x-guest-layout>
  <div class="grid min-h-screen w-screen grid-cols-1 lg:grid-cols-2">
    <!-- Left side - Login form -->
    <div class="mx-auto flex w-full max-w-3xl flex-1 flex-col justify-center px-5 py-10 sm:px-30">
      <div class="w-full">
        @if (config('journalos.show_marketing_site'))
          <p class="group mb-10 flex items-center gap-x-1 text-sm text-gray-600">
            <x-phosphor-arrow-left class="h-4 w-4 transition-transform duration-150 group-hover:-translate-x-1" />
            <x-link href="{{ route('marketing.index') }}" class="group-hover:underline">{{ __('Back to the marketing website') }}</x-link>
          </p>
        @endif

        <!-- Session status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Title -->
        <div class="mb-8">
          <div class="mb-2 flex items-center gap-x-2">
            <a href="" class="group flex items-center gap-x-2 transition-transform ease-in-out">
              <div class="flex h-7 w-7 items-center justify-center transition-all duration-400 group-hover:-translate-y-0.5 group-hover:-rotate-3">
                <x-image src="{{ asset('logo/30x30.webp') }}" srcset="{{ asset('logo/30x30.webp') }} 1x, {{ asset('logo/30x30@2x.webp') }} 2x" width="25" height="25" alt="JournalOS logo" />
              </div>
            </a>
            <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">
              {{ __('Sign up for an account') }}
            </h1>
          </div>
          <p class="text-sm text-gray-500">{{ __('You will be the administrator of this account.') }}</p>
        </div>

        <!-- Registration form -->
        <x-box class="mb-12">
          <x-form method="post" :action="route('register')" class="space-y-4">
            <!-- Name -->
            <div class="flex flex-col gap-2 sm:flex-row sm:gap-4">
              <div class="w-full">
                <x-input type="text" id="first_name" value="{{ old('first_name') }}" :label="__('First name')" required placeholder="John" :error="$errors->get('first_name')" autocomplete="first_name" />
              </div>

              <div class="w-full">
                <x-input type="text" id="last_name" value="{{ old('last_name') }}" :label="__('Last name')" required placeholder="Doe" :error="$errors->get('last_name')" autocomplete="last_name" />
              </div>
            </div>

            <!-- Email address -->
            <x-input type="email" id="email" value="{{ old('email') }}" :label="__('Email address')" required placeholder="john@doe.com" :error="$errors->get('email')" :passManagerDisabled="false" autocomplete="username" help="{{ __('We will never, ever send you marketing emails.') }}" />

            <!-- Password -->
            <div class="flex flex-col gap-2 sm:flex-row sm:gap-4">
              <div class="w-full">
                <x-input type="password" id="password" :label="__('Password')" required :error="$errors->get('password')" :passManagerDisabled="false" autocomplete="current-password" />
              </div>

              <div class="w-full">
                <x-input type="password" id="password_confirmation" :label="__('Confirm password')" required :error="$errors->get('password_confirmation')" :passManagerDisabled="false" autocomplete="new-password" />
              </div>
            </div>

            <!-- Terms and conditions -->

            @if (config('journalos.enable_anti_spam'))
              {{--
                <div class="mt-4 mb-0">
                <x-turnstile data-size="flexible" />
                
                <x-input-error :messages="$errors->get('cf-turnstile-response')" class="mt-2" />
                </div>
              --}}
            @endif

            <div class="flex items-center justify-between">
              <x-button class="w-full" data-test="register-button">{{ __('Next step: validate your email address') }}</x-button>
            </div>
          </x-form>
        </x-box>

        <!-- Register link -->
        <x-box class="mb-8 text-center text-sm">
          {{ __('Already have an account?') }}
          <x-link :href="route('login')" class="ml-1">
            {{ __('Sign in instead') }}
          </x-link>
        </x-box>

        <ul class="text-xs text-gray-600">
          <li>&copy; {{ config('app.name') }} {{ now()->format('Y') }}</li>
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
