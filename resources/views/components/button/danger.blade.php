@props([
  'href',
])

@php
  $base = 'relative inline-flex h-8 transform-gpu items-center justify-center gap-2 rounded-lg text-sm font-medium whitespace-nowrap transition duration-150 ease-out focus-visible:ring-2 focus-visible:ring-red-500/40 focus-visible:outline-none active:translate-y-[1px] active:scale-[0.97] active:shadow-inner active:ease-in disabled:pointer-events-none disabled:cursor-default disabled:opacity-75 aria-pressed:z-10 dark:disabled:opacity-75 [:where(&)]:px-3';
  $style = 'border border-red-600/90 border-b-red-700 bg-red-600 text-white shadow-xs hover:bg-red-500 dark:border-red-500 dark:border-b-red-600 dark:bg-red-500 dark:text-white dark:hover:bg-red-400';
  $classes = $base . ' ' . $style;
@endphp

@isset($href)
  <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
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
