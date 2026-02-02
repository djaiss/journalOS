<?php

/**
 * @var \App\Models\Journal $journal
 * @var \Illuminate\Support\ViewErrorBag $errors
 */
?>

<x-form id="layout-create-form" x-target="layouts-list layout-create-form notifications" x-target.back="layout-create-form" method="post" action="{{ route('journal.settings.layouts.store', ['slug' => $journal->slug]) }}" class="space-y-5 rounded-t-lg p-4 first:rounded-t-lg last:rounded-b-lg last:border-0 hover:bg-blue-50 dark:hover:bg-gray-800">
  <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_10rem]">
    <div>
      <x-input id="name" :label="__('Layout name')" :error="$errors->get('name')" :value="old('name')" required autofocus />
    </div>
    <div>
      <x-select id="columns_count" :label="__('Columns')" :options="[1 => '1', 2 => '2', 3 => '3', 4 => '4']" :selected="old('columns_count', 3)" :error="$errors->get('columns_count')" required />
    </div>
  </div>

  <div class="flex justify-between">
    <x-button.secondary href="{{ route('journal.settings.modules.index', ['slug' => $journal->slug]) }}" turbo="true" x-target="layout-create-form">
      {{ __('Cancel') }}
    </x-button.secondary>

    <x-button class="mr-2" data-test="create-layout-button">
      {{ __('Create') }}
    </x-button>
  </div>
</x-form>
