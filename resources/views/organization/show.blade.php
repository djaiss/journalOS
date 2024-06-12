<x-app-layout>
  <x-slot name="organizationContext">
    <div class="max-w-7xl mx-auto px-4 sm:px-0 py-2">
      <ul class="text-sm">
        <li class="inline after:content-['>'] after:text-gray-500 after:text-xs">
          <x-link href="{{ route('dashboard.index') }}">{{ __('Dashboard') }}</x-link>
        </li>
        <li class="inline" dusk="header-organization-name">{{ $organization->name }}</li>
      </ul>
    </div>
  </x-slot>

  <x-slot name="navigation">
    @include('layouts.navigation')
  </x-slot>

  <div class="py-12">
    <div class="max-w-5xl mx-auto px-4 sm:px-0">

      dds
    </div>
  </div>
</x-app-layout>
