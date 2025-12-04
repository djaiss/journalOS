<?php
/*
 * @var \App\Models\User $user
 * @var \App\Http\ViewModels\ProfileShowViewModel $viewModel
 */
?>

<x-app-layout>
  <x-slot:title>
    {{ __('Profile') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => __('Settings')]
  ]" />

  <!-- settings layout -->
  <div class="grid flex-grow sm:grid-cols-[220px_1fr]">
    <!-- Sidebar -->
    @include('settings.partials.sidebar')

    <!-- Main content -->
    <section class="p-4 sm:p-8">
      <div class="mx-auto flex max-w-4xl flex-col gap-y-8 sm:px-0">
        <!-- update user details -->
        @include('settings.profile.partials.details', ['user' => $user])

        <!-- logs -->
        @include('settings.profile.partials.logs', ['viewModel' => $viewModel])

        <!-- emails sent -->
        @include('settings.profile.partials.emails', ['viewModel' => $viewModel])
      </div>
    </section>
  </div>
</x-app-layout>
