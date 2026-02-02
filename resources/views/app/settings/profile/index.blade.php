<?php

/**
 * @var \App\Models\User $user
 * @var \Illuminate\Support\Collection $logs
 * @var \Illuminate\Support\Collection $emails
 * @var bool $hasMoreLogs
 * @var bool $hasMoreEmails
 * @var \Illuminate\Support\ViewErrorBag $errors
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
  <div class="grid grow bg-gray-50 sm:grid-cols-[220px_1fr] dark:bg-gray-950">
    <!-- Sidebar -->
    @include('app.settings.partials.sidebar')

    <!-- Main content -->
    <section class="p-4 sm:p-8">
      <div class="mx-auto flex max-w-4xl flex-col gap-y-8 sm:px-0">
        <!-- update user details -->
        @include('app.settings.profile.partials.details', ['user' => $user, 'errors' => $errors])

        <!-- logs -->
        @include('app.settings.profile.partials.logs', ['logs' => $logs, 'hasMoreLogs' => $hasMoreLogs])

        <!-- emails sent -->
        @include('app.settings.profile.partials.emails', ['emails' => $emails, 'hasMoreEmails' => $hasMoreEmails])
      </div>
    </section>
  </div>
</x-app-layout>
