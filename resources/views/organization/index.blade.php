<?php
/*
 * @var App\ViewModels\Organizations\OrganizationIndexViewModel $viewModel
 */
?>

<x-app-layout>
  <x-slot:title>
    {{ __('Dashboard') }}
  </x-slot>

  <div class="px-6 pt-6">
    <div class="mx-auto w-full max-w-4xl items-start justify-center">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-lg font-medium text-gray-900">{{ __('Your organizations') }}</h2>

        <x-button.secondary href="{{ route('organization.create') }}" turbo="true">
          <x-slot:icon>
            <x-phosphor-plus-bold class="size-4" />
          </x-slot>
          {{ __('New organization') }}
        </x-button.secondary>
      </div>

      <x-box padding="p-0">
        @forelse ($viewModel->organizations() as $organization)
          <div class="rounded-0 flex items-center border-b border-gray-200 text-sm first:rounded-t-lg last:rounded-b-lg last:border-b-0 hover:bg-gray-50">
            <div class="mr-2 rounded-full p-3">
              <img src="{{ $organization->avatar }}" class="h-8 w-8" alt="Avatar" />
            </div>

            <x-link href="{{ route('organization.show', $organization->slug) }}">{{ $organization->name }}</x-link>
          </div>
        @empty
          <div class="flex flex-col items-center gap-2 p-4 text-center text-gray-500">
            <div class="mb-1 rounded-full bg-gray-100 p-3">
              <x-phosphor-building-office class="size-6 text-gray-600" />
            </div>

            <p class="text-sm text-gray-500">
              {{ __('You are not a member of any organizations yet.') }}
            </p>
          </div>
        @endforelse
      </x-box>
    </div>
  </div>
</x-app-layout>
