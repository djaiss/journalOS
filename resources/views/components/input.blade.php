@props([
  'size' => 'base',
  'type' => 'text',
  'passManagerDisabled' => true,
  'required' => false,
  'id' => null,
  'label' => null,
  'autocomplete' => null,
  'error' => null,
  'placeholder' => null,
  'value' => null,
  'help' => null,
  'autofocus' => false,
  'disabled' => false,
])

@php
  $classes = [
    'block w-full appearance-none',
    'pr-3 pl-3',
    'bg-white dark:bg-white/10 dark:disabled:bg-white/[7%]',
    'text-gray-700 placeholder-gray-400 disabled:text-gray-500 disabled:placeholder-gray-400/70 dark:text-gray-300 dark:placeholder-gray-400 dark:disabled:text-gray-400 dark:disabled:placeholder-gray-500',
    'rounded-lg border border-gray-200 border-b-gray-300/80 disabled:border-b-gray-200 dark:border-white/10 dark:disabled:border-white/5',
    'shadow-xs disabled:shadow-none dark:shadow-none',
    'aria-invalid:border-red-500',
    match ($size) {
      'base' => 'h-10 py-2 text-base leading-[1.375rem] sm:text-sm',
      'sm' => 'h-8 py-1.5 text-sm leading-[1.125rem]',
      'xs' => 'h-6 py-1.5 text-xs leading-[1.125rem]',
    },
  ];
@endphp

@if ($label)
  <div class="space-y-2">
    <div class="flex items-center space-x-2">
      <x-label :for="$id" :value="$label" />
      @if (! $required)
        <span class="text-sm text-gray-500">({{ __('optional') }})</span>
      @endif
    </div>
    <input id="{{ $id }}" name="{{ $id }}" type="{{ $type }}" {{ $attributes->class($classes) }} value="{{ $value }}" {{ $autocomplete ? 'autocomplete="' . $autocomplete . '"' : '' }} placeholder="{{ $placeholder ? $placeholder : '' }}" @if($passManagerDisabled) data-1p-ignore @endif {{ $autofocus ? 'autofocus' : '' }} {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} />
    @if ($help)
      <p class="mt-1 block text-xs text-gray-700 dark:text-gray-300">{{ $help }}</p>
    @endif

    <x-error :messages="$error" />
  </div>
@else
  <div class="space-y-2">
    <input id="{{ $id }}" name="{{ $id }}" type="{{ $type }}" {{ $attributes->class($classes) }} value="{{ $value }}" {{ $autocomplete ? 'autocomplete="' . $autocomplete . '"' : '' }} placeholder="{{ $placeholder ? $placeholder : '' }}" @if($passManagerDisabled) data-1p-ignore @endif {{ $autofocus ? 'autofocus' : '' }} {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} />
    @if ($help)
      <p class="mt-1 block text-xs text-gray-700 dark:text-gray-300">{{ $help }}</p>
    @endif

    <x-error :messages="$error" />
  </div>
@endif
