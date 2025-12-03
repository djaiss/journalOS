<x-app-layout :organization="$organization">
  <x-slot:title>
    {{ __('Organization') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('organization.index')],
    ['label' => __('Organization')]
  ]" />

  {{ $organization->name }}
</x-app-layout>
