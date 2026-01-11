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

  <div class="grid grow sm:grid-cols-[220px_1fr]">
    @include('app.journal.settings.partials.sidebar', ['journal' => $journal])

    <section class="p-4 sm:p-8">
      <div class="mx-auto flex max-w-2xl flex-col gap-y-8 sm:px-0">
        @include('app.journal.settings.partials.edit-past')

        @include('app.journal.settings.partials.rename')

        @include('app.journal.settings.partials.delete')
      </div>
    </section>
  </div>
</x-app-layout>
