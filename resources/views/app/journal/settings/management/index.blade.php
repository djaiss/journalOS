<?php

/**
 * @var \App\Models\Journal $journal
 * @var \Illuminate\Support\ViewErrorBag $errors
 */
?>

<x-app-layout :journal="$journal">
  <x-slot:title>
    {{ __('Maintenance') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => $journal->name, 'route' => route('journal.show', ['slug' => $journal->slug]) ],
    ['label' => __('Settings'), 'route' => route('journal.settings.modules.index', ['slug' => $journal->slug])],
    ['label' => __('Maintenance')]
  ]" />

  <div class="grid grow bg-gray-50 sm:grid-cols-[220px_1fr] dark:bg-gray-950">
    @include('app.journal.settings.partials.sidebar', ['journal' => $journal])

    <section class="p-4 sm:p-8">
      <div class="mx-auto flex max-w-2xl flex-col gap-y-8 sm:px-0">
        @include('app.journal.settings.partials.edit-past', ['journal' => $journal, 'errors' => $errors])

        @include('app.journal.settings.partials.rename', ['journal' => $journal, 'errors' => $errors])

        @include('app.journal.settings.partials.delete', ['journal' => $journal])
      </div>
    </section>
  </div>
</x-app-layout>
