<?php
/**
 * @var \App\Models\Journal $journal
 * @var \Illuminate\Support\Collection $layouts
 * @var \Illuminate\Support\ViewErrorBag $errors
 */
?>

<x-box padding="p-0">
  <x-slot:title>{{ __('Layouts') }}</x-slot>
  <x-slot:description>
    <p>{{ __('Layouts define how modules are arranged for a journal entry.') }}</p>
    <p>{{ __('A journal entry needs at least one layout for the content to appear, otherwise it will be empty.') }}</p>
  </x-slot>

  <x-form method="post" action="{{ route('journal.settings.layouts.store', ['slug' => $journal->slug]) }}" class="border-b border-gray-200 p-3 dark:border-gray-700" x-target="layout-create-form layouts-list notifications" id="layout-create-form">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-end">
      <div class="flex-1">
        <x-input
          id="name"
          :label="__('Layout name')"
          :error="$errors->get('name')"
          :value="old('name')"
          required
        />
      </div>
      <div class="sm:w-44">
        <x-select
          id="columns_count"
          :label="__('Columns')"
          :options="[1 => '1', 2 => '2', 3 => '3', 4 => '4']"
          :selected="old('columns_count', 3)"
          :error="$errors->get('columns_count')"
          required
        />
      </div>
      <div class="sm:pb-1">
        <x-button.secondary type="submit" data-test="create-layout-button">
          {{ __('Create layout') }}
        </x-button.secondary>
      </div>
    </div>
  </x-form>

  <div id="layouts-list">
  <div class="flex items-center justify-between rounded-t-lg p-3 last:rounded-b-lg last:border-b-0 hover:bg-blue-50 dark:hover:bg-gray-800">
    @if ($layouts->isEmpty())
      <p class="text-sm text-zinc-500">{{ __('No layouts created') }}</p>
    @else
      <p class="text-sm text-zinc-500">{{ __(':count layout(s) created', ['count' => $layouts->count()]) }}</p>
    @endif
  </div>


    @if (! $layouts->isEmpty())
    @foreach ($layouts as $layout)
      <div class="group flex items-center justify-between border-b border-gray-200 p-3 first:border-t last:rounded-b-lg last:border-b-0 dark:border-gray-700">
        <div class="flex items-center gap-3">
          <div class="rounded-sm bg-zinc-100 p-2 dark:bg-gray-800">
            <x-phosphor-columns class="h-4 w-4 text-zinc-500" />
          </div>

          <div class="flex flex-col">
            <p class="text-sm font-semibold">
              {{ $layout->name }}
              @if ($layout->is_active)
                <span class="ml-2 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-200">{{ __('Active') }}</span>
              @endif
            </p>
            <p class="text-xs text-zinc-500">{{ __(':count columns', ['count' => $layout->columns_count]) }}</p>
          </div>
        </div>

        <x-form
          x-target="layouts-list"
          action="{{ route('journal.settings.layouts.destroy', ['slug' => $journal->slug, 'layout' => $layout->id]) }}"
          method="delete"
          x-on:ajax:before="
            confirm('Are you sure you want to proceed? This can not be undone.') ||
              $event.preventDefault()
          ">
          <x-button x-target="layouts-list" class="text-sm" data-test="delete-layout-{{ $layout->id }}">
            {{ __('Delete') }}
          </x-button>
        </x-form>
      </div>
    @endforeach
    @endif
  </div>

</x-box>
