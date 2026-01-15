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

  <div id="layout-create-form" class="flex items-center justify-between rounded-t-lg p-3 last:rounded-b-lg last:border-b-0 hover:bg-blue-50 dark:hover:bg-gray-800">
    @if ($layouts->isEmpty())
      <p class="text-sm text-zinc-500">{{ __('No layouts created') }}</p>
    @else
      <p class="text-sm text-zinc-500">{{ __(':count layout(s) created', ['count' => $layouts->count()]) }}</p>
    @endif

    <x-button.secondary href="{{ route('journal.settings.layouts.create', ['slug' => $journal->slug]) }}" x-target="layout-create-form" class="mr-2 text-sm" data-test="new-layout-button">
      {{ __('New layout') }}
    </x-button.secondary>
  </div>

  @if (! $layouts->isEmpty())
    <div id="layouts-list">
      @foreach ($layouts as $layout)
        <div x-data="{
          editing: {{ (int) old('layout_id') === $layout->id ? 'true' : 'false' }},
        }" class="border-b border-gray-200 first:border-t last:rounded-b-lg last:border-b-0 dark:border-gray-700">
          <div class="group flex items-center justify-between p-3">
            <div class="flex items-center gap-3">
              <div class="rounded-sm bg-zinc-100 p-2 dark:bg-gray-800">
                <x-phosphor-columns class="h-4 w-4 text-zinc-500" />
              </div>

              <div class="flex flex-col">
                <p class="text-sm font-semibold">
                  {{ $layout->name }}
                  @if ($layout->is_active)
                    <span class="ml-2 rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700 dark:bg-green-900/40 dark:text-green-200">{{ __('Default') }}</span>
                  @endif
                </p>
                <p class="text-xs text-zinc-500">{{ __(':count columns', ['count' => $layout->columns_count]) }}</p>
              </div>
            </div>

            <div class="flex items-center gap-2">
              @if (! $layout->is_active)
                <x-form x-target="layouts-list" action="{{ route('journal.settings.layouts.default', ['slug' => $journal->slug, 'layout' => $layout->id]) }}" method="put">
                  <x-button.secondary x-target="layouts-list" class="text-sm" data-test="set-default-layout-{{ $layout->id }}">
                    {{ __('Make default') }}
                  </x-button.secondary>
                </x-form>
              @endif

              <x-button.secondary type="button" x-show="!editing" x-cloak class="text-sm" x-on:click="editing = true" data-test="edit-layout-{{ $layout->id }}">
                {{ __('Edit') }}
              </x-button.secondary>
              <x-button.secondary type="button" x-show="editing" x-cloak class="text-sm" x-on:click="editing = false">
                {{ __('Cancel') }}
              </x-button.secondary>

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
          </div>

          <div x-cloak x-show="editing" x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="-translate-y-1 transform opacity-0" x-transition:enter-end="translate-y-0 transform opacity-100" x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="translate-y-0 transform opacity-100" x-transition:leave-end="-translate-y-1 transform opacity-0" class="{{ $loop->last ? 'rounded-b-lg' : '' }} border-t border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900">
            <x-form x-target="layouts-list" method="put" action="{{ route('journal.settings.layouts.update', ['slug' => $journal->slug, 'layout' => $layout->id]) }}" class="flex flex-col gap-3">
              <input type="hidden" name="layout_id" value="{{ $layout->id }}" />
              <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_10rem]">
                <div class="space-y-2">
                  <x-label for="layout-name-{{ $layout->id }}" :value="__('Layout name')" />
                  <input id="layout-name-{{ $layout->id }}" name="name" type="text" class="block h-10 w-full appearance-none rounded-lg border border-gray-200 border-b-gray-300/80 bg-white px-3 py-2 text-base leading-[1.375rem] text-gray-700 placeholder-gray-400 shadow-xs aria-invalid:border-red-500 sm:text-sm dark:border-white/10 dark:bg-white/10 dark:text-gray-300 dark:placeholder-gray-400 dark:shadow-none" value="{{ old('name', $layout->name) }}" required />
                  <x-error :messages="$errors->get('name')" />
                  <x-error :messages="$errors->get('layout_name')" />
                </div>
                <div class="space-y-2">
                  <x-label for="layout-columns-{{ $layout->id }}" :value="__('Columns')" />
                  <select id="layout-columns-{{ $layout->id }}" name="columns_count" class="block h-10 w-full appearance-none rounded-lg border border-gray-200 border-b-gray-300/80 bg-white px-3 py-2 text-base leading-[1.375rem] text-gray-700 shadow-xs aria-invalid:border-red-500 sm:text-sm dark:border-white/10 dark:bg-white/10 dark:text-gray-300 dark:shadow-none" required>
                    @foreach ([1 => '1', 2 => '2', 3 => '3', 4 => '4'] as $value => $label)
                      <option value="{{ $value }}" @selected((int) old('columns_count', $layout->columns_count) === $value)>
                        {{ $label }}
                      </option>
                    @endforeach
                  </select>
                  <x-error :messages="$errors->get('columns_count')" />
                </div>
              </div>
              <div class="flex items-center gap-2">
                <x-button.secondary type="submit" class="text-sm">
                  {{ __('Save') }}
                </x-button.secondary>
              </div>
            </x-form>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</x-box>
