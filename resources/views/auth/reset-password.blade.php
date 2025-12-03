<x-guest-layout>
  <div class="grid min-h-screen w-screen grid-cols-1 lg:grid-cols-2">
    <!-- Left side - Reset password form -->
    <div class="mx-auto flex w-full max-w-2xl flex-1 flex-col justify-center px-5 py-10 sm:px-30">
      <div class="w-full">
        @if (config('collectos.show_marketing_site'))
          <p class="group mb-10 flex items-center gap-x-1 text-sm text-gray-600">
            <x-phosphor-arrow-left class="h-4 w-4 transition-transform duration-150 group-hover:-translate-x-1" />
            <x-link href="" class="group-hover:underline">{{ __('Back to the marketing website') }}</x-link>
          </p>
        @endif

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Title -->
        <div class="mb-8 flex items-center gap-x-2">
          <a href="" class="group flex items-center gap-x-2 transition-transform ease-in-out">
            <div class="flex h-7 w-7 items-center justify-center transition-all duration-400 group-hover:-translate-y-0.5 group-hover:-rotate-3">
              <x-logo width="25" height="25" />
            </div>
          </a>
          <h1 class="text-2xl font-semibold text-gray-900">
            {{ __('Reset password') }}
          </h1>
        </div>

        <!-- Reset password form -->
        <x-box class="mb-8">
          <p class="mb-4 text-sm text-gray-600">{{ __('Please enter your new password below') }}</p>

          <x-form method="post" action="{{ route('password.store', $request->token) }}" class="space-y-4">
            <!-- Hidden token field -->
            <input type="hidden" name="token" value="{{ $request->token }}" />

            <!-- Email Address -->
            <x-input id="email" type="email" :label="__('Email')" :value="$request->email" required autocomplete="email" />

            <!-- Password -->
            <x-input id="password" type="password" :label="__('Password')" required autocomplete="new-password" />

            <!-- Confirm Password -->
            <x-input id="password_confirmation" type="password" :label="__('Confirm password')" required autocomplete="new-password" />

            <x-button class="w-full">{{ __('Reset password') }}</x-button>
          </x-form>
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
