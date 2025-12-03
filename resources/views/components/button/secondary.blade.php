@props([
  'href',
  'turbo' => false,
])

@php
  $base = 'relative inline-flex h-8 transform-gpu items-center justify-center gap-2 rounded-lg text-sm font-medium whitespace-nowrap transition duration-150 ease-out active:translate-y-[1px] active:scale-[0.97] active:shadow-inner active:ease-in disabled:pointer-events-none disabled:cursor-default disabled:opacity-75 aria-pressed:z-10 dark:disabled:opacity-75 [:where(&)]:px-3';
  $visual = 'border border-gray-300 border-b-gray-300/80 bg-white text-gray-800 shadow-xs hover:border-gray-400 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:border-gray-600 dark:hover:bg-gray-600/75';
  $classes = $base . ' ' . $visual;
@endphp

@isset($href)
  <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }} @if($turbo) data-turbo="true" @endif>
    @isset($icon)
      <span class="shrink-0">{{ $icon }}</span>
    @endisset

    {{ $slot }}
  </a>
@else
  <button type="submit" {{ $attributes->merge(['class' => $classes]) }}>
    @isset($icon)
      <span class="shrink-0">{{ $icon }}</span>
    @endisset

    {{ $slot }}
  </button>
@endif
