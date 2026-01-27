<x-box padding="p-0">
  <x-slot:title>
    {{ __('LLM access') }}
  </x-slot>

  <x-slot:description>
    {{ __('You can access your account with an LLM like ChatGPT or Claude. Basically, if you activate it, we will give you a link that will let you query a specific day or a month and get in return an LLM-friendly response. With that you will be able to get stats or whatever you want with it. This URL is quite secure, but we strongly suggest that you keep it private and be very careful with it as it can access, in read mode, all your data.') }}
  </x-slot>

  <x-form method="put" x-target="preferred-method-form notifications" x-target.back="preferred-method-form" id="preferred-method-form" :action="route('settings.security.2fa.update')">
    <div class="grid grid-cols-3 items-center rounded-t-lg border-b border-gray-200 p-3 last:rounded-b-lg hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
      <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Preferred methods') }}</p>
      <div class="col-span-1 w-full justify-self-end">
        <x-select id="auto_delete_account" :options="[
          'yes' => __('Yes'),
          'no' => __('No'),
        ]" selected="{{ old('auto_delete_account', auth()->user()->auto_delete_account ? 'yes' : 'no') }}" :error="$errors->get('auto_delete_account')" />
      </div>
    </div>

    <!-- actions -->
    <div class="flex items-center justify-end p-3">
      <x-button>{{ __('Save') }}</x-button>
    </div>
  </x-form>
</x-box>
