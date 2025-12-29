<x-box padding="p-0">
  <x-slot:title>{{ __('Logs') }}</x-slot:title>
  <x-slot:description>
    <p>{{ __('All actions performed on your account are logged here.') }}</p>
  </x-slot:description>

  <!-- last actions -->
  @foreach ($logs as $log)
    <div class="flex items-center justify-between border-b border-gray-200 p-3 text-sm first:rounded-t-lg last:rounded-b-lg last:border-b-0 hover:bg-blue-50">
      <div class="flex items-center gap-3">
        <x-phosphor-pulse class="size-3 min-w-3 text-zinc-600 dark:text-zinc-400" />
        <div class="flex flex-col gap-y-2">
          <p class="items-center gap-2 sm:flex">
            @if ($log->journal_name)
              <x-link href="{{ route('journal.show', $log->journal_id) }}">{{ $log->journal_name }}</x-link>
              |
            @endif

            <span class="font-mono text-xs">{{ $log->action }}</span>
          </p>
          <p class="">{{ $log->description }}</p>
        </div>
      </div>

      <x-tooltip text="{{ $log->created_at }}">
        <p class="font-mono text-xs">{{ $log->created_at->diffForHumans() }}</p>
      </x-tooltip>
    </div>
  @endforeach

  @if ($hasMoreLogs)
    <div class="flex justify-center rounded-b-lg p-3 text-sm">
      <x-link href="{{ route('settings.logs.index') }}" class="text-center">{{ __('Browse all activity') }}</x-link>
    </div>
  @endif
</x-box>
