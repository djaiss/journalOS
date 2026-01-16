<?php
/**
 * @var \App\Models\Journal $journal
 * @var \Illuminate\Support\Collection $layouts
 */
?>

<x-app-layout :journal="$journal">
  <x-slot:title>
    {{ __('Modules') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => $journal->name, 'route' => route('journal.show', ['slug' => $journal->slug]) ],
    ['label' => __('Modules')]
  ]" />

  <div class="grid grow bg-gray-50 sm:grid-cols-[220px_1fr] dark:bg-gray-950">
    @include('app.journal.settings.partials.sidebar', ['journal' => $journal])

    <section class="p-4 sm:p-8">
      <div class="mx-auto flex max-w-3xl flex-col gap-y-8 sm:px-0">
        @include('app.journal.settings.partials.layouts.index', ['journal' => $journal, 'layouts' => $layouts, 'errors' => $errors])
      </div>
    </section>
  </div>
</x-app-layout>
