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

        <!-- Title -->
        <div class="mb-8">
          <div class="flex items-center gap-x-2">
            <a href="" class="group flex items-center gap-x-2 transition-transform ease-in-out">
              <div class="flex h-7 w-7 items-center justify-center transition-all duration-400 group-hover:-translate-y-0.5 group-hover:-rotate-3">
                <x-image src="{{ asset('logo/30x30.webp') }}" srcset="{{ asset('logo/30x30.webp') }} 1x, {{ asset('logo/30x30@2x.webp') }} 2x" width="25" height="25" alt="Memoir logo" />
              </div>
            </a>
            <h1 class="mb-2 text-2xl font-semibold text-gray-900">
              {{ __('Receive a link to login') }}
            </h1>
          </div>
          <p class="text-sm text-gray-500">{{ __('Enter your email below and we will send you a link to magically connect to your account.') }}</p>
        </div>

        <!-- Login form -->
        <x-box class="mb-8">
          <p class="text-sm text-gray-500">{{ __('We\'ve sent you a temporary login link. This link is valid for 5 minutes. Please check your inbox.') }}</p>
        </x-box>

        <!-- Register link -->
        <x-box class="mb-8 text-center text-sm">
          {{ __('Want to use your password instead?') }}
          <x-link :href="route('login')" class="ml-1">
            {{ __('Back to login') }}
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
