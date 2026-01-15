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
            <!-- module name -->
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

            <!-- buttons -->
            <div class="flex items-center gap-2">
              <x-button.secondary type="button" x-show="!editing" x-cloak class="text-sm" x-on:click="editing = true" data-test="edit-layout-{{ $layout->id }}">
                {{ __('Edit modules') }}
              </x-button.secondary>

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

          @include('app.journal.settings.partials.layouts.edit', ['isLast' => $loop->last])
        </div>
      @endforeach
    </div>
  @endif
</x-box>
