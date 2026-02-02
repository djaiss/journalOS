<?php

/**
 * @var string $name
 * @var string $value
 * @var string $xTarget
 * @var string $action
 * @var string|bool $selected
 */
?>

@props([
  'name' => '',
  'value' => '',
  'x-target' => '',
  'action' => '',
  'selected' => '',
])

<div class="flex-1 border-r border-gray-200 first:overflow-hidden first:rounded-l-lg last:rounded-r-lg last:border-r-0 dark:border-gray-700">
  <x-form x-target="{{ $xTarget }}" :action="$action" method="put" class="h-full">
    <input type="hidden" name="{{ $name }}" value="{{ $value }}" />
    <button type="submit" class="{{ $selected ? 'bg-red-50 font-bold dark:bg-red-900/40' : '' }} flex h-full w-full cursor-pointer items-center justify-center p-2 text-center first:rounded-l-lg last:rounded-r-lg hover:bg-red-50 dark:hover:bg-red-900/40">
      {{ __('No') }}
    </button>
  </x-form>
</div>
