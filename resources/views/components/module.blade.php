@props([
  'title' => null,
  'emoji' => null,
])

<div {{ $attributes->merge(['class' => 'rounded-lg border border-gray-200 bg-white ']) }}>
  @isset($title)
  <div class="border-b border-gray-200 flex items-center p-2">
    @isset($emoji)
    <span class="mr-2">{{ $emoji }}</span>
    @endisset
    <h2 class="font-semibold">{{ $title }}</h2>
  </div>
  @endisset

  <div class="p-2">
  {{ $slot }}
  </div>
</div>
