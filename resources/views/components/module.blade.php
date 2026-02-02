<?php

/**
 * @var string|null $title
 * @var string|null $emoji
 * @var \Illuminate\View\ComponentSlot|null $action
 * @var \Illuminate\View\ComponentAttributeBag $attributes
 * @var \Illuminate\View\ComponentSlot $slot
 */
?>

@props([
  'title' => null,
  'emoji' => null,
  'action' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 ']) }}>
  <div class="flex items-center justify-between border-b border-gray-200 p-2 dark:border-gray-700">
    @isset($title)
      <div class="flex items-center">
        @isset($emoji)
          <span class="mr-2">{{ $emoji }}</span>
        @endisset

        <h2 class="font-semibold">{{ $title }}</h2>
      </div>
    @endisset

    @isset($action)
      {{ $action }}
    @endisset
  </div>

  <div class="p-2">
    {{ $slot }}
  </div>
</div>
