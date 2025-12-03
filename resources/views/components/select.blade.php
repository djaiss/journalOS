@props([
  'required' => false,
  'id' => null,
  'label' => null,
  'error' => null,
  'value' => null,
  'help' => null,
  'options' => [],
  'selected' => null,
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
    'h-10 py-2 text-base leading-[1.375rem] sm:text-sm',
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

    <select id="{{ $id }}" name="{{ $id }}" {{ $attributes->class($classes) }} {{ $required ? 'required' : '' }}>
      @foreach ($options as $value => $label)
        <option value="{{ $value }}" @selected($value === $selected)>{{ $label }}</option>
      @endforeach
    </select>
    @if ($help)
      <p class="mt-1 block text-xs text-gray-700 dark:text-gray-300">{{ $help }}</p>
    @endif

    <x-error :messages="$error" />
  </div>
@else
  <div class="space-y-2">
    <select id="{{ $id }}" name="{{ $id }}" {{ $attributes->class($classes) }} {{ $required ? 'required' : '' }}>
      @foreach ($options as $value => $label)
        <option value="{{ $value }}" @selected($value === $selected)>{{ $label }}</option>
      @endforeach
    </select>
    @if ($help)
      <p class="mt-1 block text-xs text-gray-700 dark:text-gray-300">{{ $help }}</p>
    @endif

    <x-error :messages="$error" />
  </div>
@endif
