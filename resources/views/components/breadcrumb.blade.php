@props([
  'items',
])

<div class="flex w-full rounded-t-lg border-b border-[#E6E7E9] bg-white px-4 py-2">
  <div class="flex gap-x-2">
    <p class="text-gray-500">{{ __('You are here:') }}</p>
    @foreach ($items as $item)
      @if (isset($item['route']))
        <x-link href="{{ $item['route'] }}">{{ $item['label'] }}</x-link>
      @else
        <p class="text-gray-500">{{ $item['label'] }}</p>
      @endif
      @if (! $loop->last)
        <p class="text-gray-500">/</p>
      @endif
    @endforeach
  </div>
</div>
