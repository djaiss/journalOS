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
      <div class="flex w-full flex-col gap-y-8">
        @include('app.journal.settings.partials.layouts', ['journal' => $journal, 'layouts' => $layouts, 'errors' => $errors])
        @include('app.journal.settings.partials.modules', ['journal' => $journal])
      </div>
    </section>
  </div>
</x-app-layout>
