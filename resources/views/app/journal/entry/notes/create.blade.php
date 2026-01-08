<x-app-layout :journal="$journal">
  <x-slot:title>
    {{ __('Add a note') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => $journal->name, 'route' => route('journal.show', ['slug' => $journal->slug]) ],
    ['label' => $entry->getDate(), 'route' => route('journal.entry.show', [
      'slug' => $journal->slug,
      'year' => $entry->year,
      'month' => $entry->month,
      'day' => $entry->day,
    ]) ],
    ['label' => __('Add a note')]
  ]" />

  <div class="mx-auto flex w-6xl flex-col sm:px-0 py-8 h-[calc(100vh-10rem)]">
    <h1 class="text-2xl text-gray-900 dark:text-gray-100 text-center mb-8">
      {{ $entry->getDate() }}
    </h1>

    <x-box class="flex-1 flex flex-col">
      <x-trix-input id="notes" name="notes" class="block w-full h-full" :value="old('notes', $entry->notes?->toTrixHtml())" autocomplete="off" />
      <x-error :messages="$errors->get('notes')" />
    </x-box>
  </div>
</x-app-layout>
