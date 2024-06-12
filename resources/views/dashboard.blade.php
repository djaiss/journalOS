<x-app-layout>
  <x-slot name="navigation">
    @include('layouts.navigation')
  </x-slot>

  <div class="py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-0">

      <div class="flex sm:flex-row flex-col items-center justify-between mb-10 sm:mb-5">
        <h1 class="text-xl mb-3 sm:mb-0">{{ __('All the organizations you are part of') }}</h1>
        <x-primary-link href="{{ route('organization.new') }}" dusk="cta-create-organization">
          <x-heroicon-c-plus class="w-4 h-4 mr-1" />
          <span>{{ __('Create a new organization') }}</span>
        </x-primary-link>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        @foreach ($data['organizations'] as $organization)
        <div class="p-4 sm:p-4 bg-white dark:bg-gray-800 shadow rounded-lg">
          <x-link href="{{ $organization['url']['show'] }}" dusk="organization-{{ $organization['id'] }}">{{ $organization['name'] }}</x-link>
        </div>
        @endforeach
      </div>

      @if (count($data['organizations']) === 0)
      <x-empty-state class="sm:p-10">
        <div class="flex justify-center">
          <x-heroicon-o-building-storefront class="w-8 h-8 text-lime-600 mb-3" />
        </div
        <p>{{ __('Create your first organization, or join an existing one.') }}</p>
      </x-empty-state>
      @endif
    </div>
  </div>
</x-app-layout>
