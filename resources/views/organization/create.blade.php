<x-app-layout>
  <x-slot:title>
    {{ __('Create organization') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('organization.index')],
    ['label' => __('Create organization')]
  ]" />

  <div class="px-6 pt-12">
    <div class="mx-auto w-full max-w-xl items-start justify-center">
      <x-box>
        <x-slot:title>{{ __('Create organization') }}</x-slot>

        <x-form method="post" :action="route('organization.store')" class="space-y-4">
          <x-input id="organization_name" name="organization_name" :label="__('Name')" :help="__('The name can only contain letters, numbers, spaces, hyphens, and underscores.')" :error="$errors->get('organization_name')" required placeholder="Dunder Mifflin" autofocus />

          <div class="flex items-center justify-between">
            <x-button.secondary href="{{ route('organization.index') }}" turbo="true">
              {{ __('Cancel') }}
            </x-button.secondary>

            <x-button type="submit">
              {{ __('Create') }}
            </x-button>
          </div>
        </x-form>
      </x-box>
    </div>
  </div>
</x-app-layout>
