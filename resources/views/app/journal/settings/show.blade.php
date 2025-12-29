<x-app-layout :journal="$journal">
  <x-slot:title>
    {{ __('Journal settings') }}
  </x-slot:title>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => $journal->name, 'route' => route('journal.show', ['slug' => $journal->slug]) ],
    ['label' => __('Settings')]
  ]" />

  <div class="mx-auto mt-10 w-xl space-y-8">
    @include('app.journal.settings.partials.rename')

    @include('app.journal.settings.partials.delete')
  </div>
</x-app-layout>
