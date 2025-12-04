<x-box class="mb-8" padding="p-0">
  <x-slot:title>{{ __('Two-factor authentication') }}</x-slot>
  <x-slot:description>
    <p>{{ __('Two-factor authentication adds an additional layer of security to your account by requiring more than just a password to sign in.') }}</p>
    <p>{{ __('Set your preferred method to use for two-factor authentication when signing into the application.') }}</p>
  </x-slot>

  <x-form method="put" x-target="preferred-method-form notifications" x-target.back="preferred-method-form" id="preferred-method-form" :action="route('settings.security.2fa.update')">
    <!-- preferred methods -->
    <div class="grid grid-cols-3 items-center rounded-t-lg border-b border-gray-200 p-3 last:rounded-b-lg hover:bg-blue-50">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Preferred methods') }}</p>
      <div class="col-span-1 w-full justify-self-end">
        <x-select id="preferred_method" :options="[
          App\Enums\TwoFactorType::NONE->value => __('None'),
          App\Enums\TwoFactorType::AUTHENTICATOR->value => __('Authenticator app'),
          App\Enums\TwoFactorType::EMAIL->value => __('Code by email')
        ]" selected="{{ old('preferred_method', $preferredMethod ?? App\Enums\TwoFactorType::NONE->value) }}" required :error="$errors->get('preferred_method')" />
      </div>
    </div>

    <!-- actions -->
    <div class="flex items-center justify-end p-3">
      <x-button>{{ __('Save') }}</x-button>
    </div>
  </x-form>
</x-box>
