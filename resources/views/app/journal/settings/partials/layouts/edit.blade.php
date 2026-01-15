<?php
/**
 * @var \App\Models\Journal $journal
 * @var \App\Models\Layout $layout
 * @var bool $isLast
 * @var \Illuminate\Support\ViewErrorBag $errors
 */
?>

<div x-cloak x-show="editing" x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="-translate-y-1 transform opacity-0" x-transition:enter-end="translate-y-0 transform opacity-100" x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="translate-y-0 transform opacity-100" x-transition:leave-end="-translate-y-1 transform opacity-0" class="{{ $isLast ? 'rounded-b-lg' : '' }} border-t border-gray-200 bg-gray-50 p-3 dark:border-gray-700 dark:bg-gray-900">
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
