<x-app-layout :journal="$journal">
  <x-slot:title>
    {{ __('Journal settings') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => $journal->name, 'route' => route('journal.show', ['slug' => $journal->slug]) ],
    ['label' => __('Settings')]
  ]" />

  <div class="mx-auto mt-10 w-xl">
    @include('app.journal.settings.partials.rename')
  </div>
</x-app-layout>
