<x-app-layout :journal="$journal">
  <x-slot:title>
    {{ __('Journal settings') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => $journal->name, 'route' => route('journal.show', ['slug' => $journal->slug]) ],
    ['label' => __('Settings')]
  ]" />

  <div class="mx-auto my-10 w-xl space-y-8">
    @include('app.journal.settings.partials.modules')

    @include('app.journal.settings.partials.rename')

    @include('app.journal.settings.partials.delete')
  </div>
</x-app-layout>
