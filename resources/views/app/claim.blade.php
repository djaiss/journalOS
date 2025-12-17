<x-app-layout>
  <x-slot:title>
    {{ __('Claim your account') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => __('Claim your account')]
  ]" />

  <div class="px-6 pt-12">
    <div class="mx-auto w-full max-w-5xl">
      <div class="grid grid-cols-1 gap-12 lg:grid-cols-2">
        <!-- Left: Form -->
        <div>
          <x-box>
            <x-slot:title>{{ __('Claim your account and make it permanent') }}</x-slot>

            <x-form method="post" :action="route('claim.store')" class="space-y-4">
              <!-- First and Last Name -->
              <div class="flex flex-col gap-2 sm:flex-row sm:gap-4">
                <div class="w-full">
                  <x-input type="text" id="first_name" name="first_name" value="{{ old('first_name') }}" :label="__('First name')" required placeholder="John" :error="$errors->get('first_name')" autocomplete="given-name" autofocus />
                </div>

                <div class="w-full">
                  <x-input type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" :label="__('Last name')" required placeholder="Doe" :error="$errors->get('last_name')" autocomplete="family-name" />
                </div>
              </div>

              <!-- Email address -->
              <x-input type="email" id="email" name="email" value="{{ old('email') }}" :label="__('Email address')" required placeholder="john@doe.com" :error="$errors->get('email')" :passManagerDisabled="false" autocomplete="username" help="{{ __('We will never, ever send you marketing emails.') }}" />

              <!-- Password -->
              <div class="flex flex-col gap-2 sm:flex-row sm:gap-4">
                <div class="w-full">
                  <x-input type="password" id="password" name="password" :label="__('Password')" required :error="$errors->get('password')" :passManagerDisabled="false" autocomplete="new-password" />
                </div>

                <div class="w-full">
                  <x-input type="password" id="password_confirmation" name="password_confirmation" :label="__('Confirm password')" required :error="$errors->get('password_confirmation')" :passManagerDisabled="false" autocomplete="new-password" />
                </div>
              </div>

              <div class="flex items-center justify-between pt-4">
                <x-button.secondary href="{{ route('journal.index') }}" turbo="true">
                  {{ __('Back to demo') }}
                </x-button.secondary>

                <x-button type="submit">
                  {{ __('Claim account') }}
                </x-button>
              </div>
            </x-form>
          </x-box>
        </div>

        <!-- Right: Marketing text -->
        <div class="flex flex-col justify-center gap-10">
          <x-box class="relative space-y-3 pt-14" padding="p-6">
            <x-image src="{{ asset('images/regis.webp') }}" alt="One-time fee" width="50" height="50" class="absolute top-0 left-1/2 h-16 w-16 -translate-x-1/2 -translate-y-1/2 rounded-full object-cover shadow-sm ring-2 ring-white" srcset="{{ asset('images/regis.webp') }} 1x, {{ asset('images/regis@2x.webp') }} 2x" />
            <p class="text-center text-4xl">👋</p>
            <p>{{ __('I\'m Régis. I\'ve created JournalOS.') }}</p>
            <p>{{ __('Thanks so much for considering creating an account.') }}</p>
            <p>{{ __('I hope you will find this tool useful. It is for me. It\'s a labor of love.') }}</p>
            <p>{{ __('Have fun!') }}</p>
          </x-box>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
