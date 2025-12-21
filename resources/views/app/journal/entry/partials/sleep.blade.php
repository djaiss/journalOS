<x-module>
  <x-slot:title>{{ __('Sleep tracking') }}</x-slot>
  <x-slot:emoji>🌖</x-slot>

  <div id="sleep-container" class="space-y-4">
    <div class="space-y-2">
      <div class="flex items-center justify-between">
        <p>{{ __('Time you went to sleep') }}</p>
        <div class="flex gap-1">
          <a x-target="sleep-container" href="{{ $data['sleep']['previous_bedtime_url'] }}" class="size-4 cursor-pointer text-gray-400 hover:text-gray-600">
            <x-phosphor-caret-left class="size-4 cursor-pointer text-gray-400 hover:text-gray-600" />
          </a>
          <a x-target="sleep-container" href="{{ $data['sleep']['next_bedtime_url'] }}" class="size-4 cursor-pointer text-gray-400 hover:text-gray-600">
            <x-phosphor-caret-right class="size-4 cursor-pointer text-gray-400 hover:text-gray-600" />
          </a>
        </div>
      </div>
      <div class="flex w-full rounded-lg border border-gray-200">
        @foreach ($data['sleep']['bedtime'] as $option)
          <div class="flex-1 cursor-pointer border-r border-gray-200 p-2 text-center first:rounded-l-lg last:rounded-r-lg last:border-r-0 hover:bg-green-50">{{ $option['formatted'] }}</div>
        @endforeach
      </div>
    </div>
    <div class="space-y-2">
      <div class="flex items-center justify-between">
        <p>{{ __('Time you woke up') }}</p>
        <div class="flex gap-1">
          <a x-target="sleep-container" href="{{ $data['sleep']['previous_wake_up_time_url'] }}" class="size-4 cursor-pointer text-gray-400 hover:text-gray-600">
            <x-phosphor-caret-left class="size-4 cursor-pointer text-gray-400 hover:text-gray-600" />
          </a>
          <a x-target="sleep-container" href="{{ $data['sleep']['next_wake_up_time_url'] }}" class="size-4 cursor-pointer text-gray-400 hover:text-gray-600">
            <x-phosphor-caret-right class="size-4 cursor-pointer text-gray-400 hover:text-gray-600" />
          </a>
        </div>
      </div>
      <div class="flex w-full rounded-lg border border-gray-200">
        @foreach ($data['sleep']['wake_up_time'] as $option)
          <div class="flex-1 cursor-pointer border-r border-gray-200 p-2 text-center first:rounded-l-lg last:rounded-r-lg last:border-r-0 hover:bg-green-50">{{ $option['formatted'] }}</div>
        @endforeach
      </div>
    </div>
  </div>
</x-module>
