@props([
  'value',
])

<label {{ $attributes->class(['block text-sm leading-tight font-medium text-gray-800 dark:text-white']) }}>{{ $value ?? $slot }}</label>
