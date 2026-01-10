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

  <div class="mx-auto flex h-[calc(100vh-10rem)] w-6xl flex-col py-8 sm:px-0">
    <h1 class="mb-8 text-center text-2xl text-gray-900 dark:text-gray-100">
      {{ $entry->getDate() }}
    </h1>

    <x-box class="flex flex-1 flex-col">
      <x-form method="put" :action="route('journal.entry.notes.update', [
        'slug' => $journal->slug,
        'year' => $entry->year,
        'month' => $entry->month,
        'day' => $entry->day,
      ])" class="flex h-full flex-col gap-4">
        <div class="flex-1">
          <x-trix-input id="notes" name="notes" class="block h-full w-full" :value="old('notes', $entry->notes?->toTrixHtml())" autocomplete="off" />
          <x-error :messages="$errors->get('notes')" />
        </div>

        <div class="mt-10 flex items-center justify-between">
          <x-button.secondary
            href="{{ route('journal.entry.show', [
            'slug' => $journal->slug,
            'year' => $entry->year,
            'month' => $entry->month,
            'day' => $entry->day,
          ]) }}"
            turbo="true">
            {{ __('Cancel') }}
          </x-button.secondary>

          <x-button type="submit">
            {{ __('Save') }}
          </x-button>
        </div>
      </x-form>
    </x-box>
  </div>
</x-app-layout>
