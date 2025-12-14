<x-guest-layout>
  <div class="grid min-h-screen w-screen grid-cols-1 lg:grid-cols-2">
    <!-- Left side - Login form -->
    <div class="mx-auto flex w-full max-w-2xl flex-1 flex-col justify-center px-5 py-10 sm:px-30">
      <div class="w-full">
        @if (config('journalos.show_marketing_site'))
          <p class="group mb-10 flex items-center gap-x-1 text-sm text-gray-600">
            <x-phosphor-arrow-left class="h-4 w-4 transition-transform duration-150 group-hover:-translate-x-1" />
            <x-link href="" class="group-hover:underline">{{ __('Back to the marketing website') }}</x-link>
          </p>
        @endif

        <!-- Session status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <!-- Title -->
        <div class="mb-8 flex items-center gap-x-2">
          <a href="" class="group flex items-center gap-x-2 transition-transform ease-in-out">
            <div class="flex h-7 w-7 items-center justify-center transition-all duration-400 group-hover:-translate-y-0.5 group-hover:-rotate-3">
              <x-image src="{{ asset('logo/30x30.webp') }}" srcset="{{ asset('logo/30x30.webp') }} 1x, {{ asset('logo/30x30@2x.webp') }} 2x" width="25" height="25" alt="Memoir logo" />
            </div>
          </a>
          <h1 class="text-2xl font-semibold text-gray-900">
            {{ __('Welcome back') }}
          </h1>
        </div>

        <!-- Login form -->
        <x-box class="mb-8">
          <x-form method="post" :action="route('login')" class="space-y-4">
            <!-- Email address -->
            <x-input type="email" id="email" value="{{ old('email') }}" :label="__('Email address')" required placeholder="john@doe.com" :error="$errors->get('email')" :passManagerDisabled="false" autocomplete="username" autofocus />

            <!-- Password -->
            <x-input type="password" id="password" :label="__('Password')" required :error="$errors->get('password')" :passManagerDisabled="false" autocomplete="current-password" />

            <!-- Remember me -->
            <div class="block">
              <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded-sm border-gray-300 text-indigo-600 shadow-xs focus:ring-indigo-500" name="remember" />
                <span class="ms-2 text-sm text-gray-600">
                  {{ __('Remember me') }}
                </span>
              </label>
            </div>

            @if (config('journalos.enable_anti_spam'))
              {{--
                <div class="mt-4 mb-0">
                <x-turnstile data-size="flexible" />

                <x-input-error :messages="$errors->get('cf-turnstile-response')" class="mt-2" />
                </div>
              --}}
            @endif

            <div class="flex items-center justify-between">
              <x-link href="{{ route('password.request') }}" class="text-sm text-gray-600">
                {{ __('Forgot your password?') }}
              </x-link>

              <x-button data-test="login-button">{{ __('Continue') }}</x-button>
            </div>
          </x-form>
        </x-box>

        <!-- local login link -->
        @env('local')
          <x-box class="mb-8 text-center text-sm">
            <x-login-link label="Michael Scott" email="michael.scott@dundermifflin.com" redirect-url="{{ route('journal.index') }}" />
          </x-box>
        @endenv

        <!-- magic link -->
        <x-box class="mb-8 text-center text-sm">
          {{ __('Wanna skip the password?') }}
          <x-link :href="route('magic.link')" data-test="magic-link-link" class="ml-1">
            {{ __('Send me a link instead') }}
          </x-link>
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
