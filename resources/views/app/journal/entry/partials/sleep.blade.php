<x-module>
  <x-slot:title>{{ __('Sleep tracking') }}</x-slot>
  <x-slot:emoji>🌖</x-slot>

  <div id="sleep-container" class="space-y-4">
    <div class="space-y-2">
      <!-- Time you went to sleep -->
      <div class="flex items-center justify-between">
        <p>{{ __('Time you went to sleep') }}</p>
        <div class="flex gap-1">
          <a x-target="sleep-container" href="{{ $module['previous_bedtime_url'] }}" class="size-4 cursor-pointer text-gray-400 hover:text-gray-600">
            <x-phosphor-caret-left class="size-4 cursor-pointer text-gray-400 hover:text-gray-600" />
          </a>
          <a x-target="sleep-container" href="{{ $module['next_bedtime_url'] }}" class="size-4 cursor-pointer text-gray-400 hover:text-gray-600">
            <x-phosphor-caret-right class="size-4 cursor-pointer text-gray-400 hover:text-gray-600" />
          </a>
        </div>
      </div>
      <div class="flex w-full rounded-lg border border-gray-200">
        @foreach ($module['bedtime'] as $option)
          <div class="flex-1 border-r border-gray-200 first:overflow-hidden first:rounded-l-lg last:rounded-r-lg last:border-r-0">
            <x-form x-target="sleep-container notifications" :action="$module['bedtime_update_url']" method="put" class="h-full">
              <input type="hidden" name="bedtime" value="{{ $option['time'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold' : '' }} flex h-full w-full cursor-pointer items-center justify-center p-2 text-center hover:bg-green-50">
                {{ \App\Helpers\TimeHelper::format($option['time']) }}
              </button>
            </x-form>
          </div>
        @endforeach
      </div>
    </div>

    <!-- Time you woke up -->
    <div class="space-y-2">
      <div class="flex items-center justify-between">
        <p>{{ __('Time you woke up') }}</p>
        <div class="flex gap-1">
          <a x-target="sleep-container" href="{{ $module['previous_wake_up_url'] }}" class="size-4 cursor-pointer text-gray-400 hover:text-gray-600">
            <x-phosphor-caret-left class="size-4 cursor-pointer text-gray-400 hover:text-gray-600" />
          </a>
          <a x-target="sleep-container" href="{{ $module['next_wake_up_url'] }}" class="size-4 cursor-pointer text-gray-400 hover:text-gray-600">
            <x-phosphor-caret-right class="size-4 cursor-pointer text-gray-400 hover:text-gray-600" />
          </a>
        </div>
      </div>
      <div class="flex w-full rounded-lg border border-gray-200">
        @foreach ($module['wake_up_time'] as $option)
          <div class="flex-1 border-r border-gray-200 first:overflow-hidden first:rounded-l-lg last:rounded-r-lg last:border-r-0">
            <x-form x-target="sleep-container notifications" :action="$module['wake_up_time_update_url']" method="put" class="h-full">
              <input type="hidden" name="wake_up_time" value="{{ $option['time'] }}" />
              <button type="submit" class="{{ $option['is_selected'] ? 'bg-green-50 font-bold' : '' }} flex h-full w-full cursor-pointer items-center justify-center p-2 text-center hover:bg-green-50">
                {{ \App\Helpers\TimeHelper::format($option['time']) }}
              </button>
            </x-form>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</x-module>
