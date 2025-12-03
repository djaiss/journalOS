@props([
  'href',
])

@php
  $base = 'relative inline-flex h-8 cursor-pointer items-center justify-center gap-2 rounded-lg text-sm font-medium whitespace-nowrap transition-[transform,box-shadow] duration-150 focus-visible:ring-2 focus-visible:outline-none active:scale-[0.97] disabled:pointer-events-none disabled:cursor-default disabled:opacity-75 aria-pressed:z-10 dark:disabled:opacity-75 [:where(&)]:px-3';
  $visual = 'border border-black/10 bg-[var(--color-accent)] text-[var(--color-accent-foreground)] shadow-[inset_0px_1px_--theme(--color-white/.2)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] focus-visible:ring-[var(--color-accent)]/50 active:shadow-[inset_0_2px_4px_0_rgba(0,0,0,0.35),inset_0_0_0_1px_rgba(0,0,0,0.25)] dark:border-0';
  $classes = $base . ' ' . $visual;
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
