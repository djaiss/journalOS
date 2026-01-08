<x-app-layout :journal="$journal">
  <x-slot:title>
    {{ __('Modules') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => $journal->name, 'route' => route('journal.show', ['slug' => $journal->slug]) ],
    ['label' => __('Settings'), 'route' => route('journal.settings.modules.index', ['slug' => $journal->slug])],
    ['label' => __('Modules')]
  ]" />

  <div class="grid grow sm:grid-cols-[220px_1fr]">
    @include('app.journal.settings.partials.sidebar', ['journal' => $journal])

    <section class="p-4 sm:p-8">
      <div class="flex w-full flex-col gap-y-8">
        @include('app.journal.settings.partials.modules')
      </div>
    </section>
  </div>
</x-app-layout>
