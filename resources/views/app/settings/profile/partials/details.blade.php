<?php
/*
 * @var \App\Models\User $user
 */
?>

<x-box>
  <x-slot:title>{{ __('Details') }}</x-slot>

  <div class="grid grid-cols-1 gap-8 sm:grid-cols-2">
    <div class="space-y-2">
      <p class="text-sm text-gray-500">{{ __('These are the details that will be displayed on your profile. Everyone within organizations you belong to will have the opportunity to view them.') }}</p>
      <p class="text-sm text-gray-500">{{ __('If you provide a nickname, it will be displayed to others instead of your real name.') }}</p>
      <p class="text-sm text-gray-500">{{ __('If you change your email address, you will need to verify it again. In this case, you will receive a new verification link.') }}</p>
    </div>

    <x-form method="put" :action="route('settings.profile.update')" class="space-y-4">
      <!-- First name -->
      <x-input id="first_name" value="{{ old('first_name', $user->first_name) }}" :label="__('First name')" required placeholder="John" :error="$errors->get('first_name')" autofocus />

      <!-- Last name -->
      <x-input id="last_name" value="{{ old('last_name', $user->last_name) }}" :label="__('Last name')" required placeholder="Doe" :error="$errors->get('last_name')" />

      <!-- nickname -->
      <x-input id="nickname" value="{{ old('nickname', $user?->nickname) }}" :label="__('Nickname')" :error="$errors->get('nickname')" />

      <!-- email -->
      <x-input id="email" value="{{ old('email', $user->email) }}" :label="__('Email')" required placeholder="john@doe.com" :error="$errors->get('email')" />

      <!-- locale -->
      <x-select id="locale" :label="__('Language')" :options="['en' => __('English'), 'fr' => __('French')]" selected="{{ $user->locale }}" required :error="$errors->get('locale')" />

      <div class="flex items-center justify-end">
        <x-button>{{ __('Save') }}</x-button>
      </div>
    </x-form>
  </div>
</x-box>
