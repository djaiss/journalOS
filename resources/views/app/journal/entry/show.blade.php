<?php
/**
 * @var \App\Models\Journal $journal
 * @var \App\Models\JournalEntry $entry
 * @var string $entryDate
 * @var string|null $notesMarkdown
 * @var array<int, array{
 *   key: string,
 *   emoji: string,
 *   title: string,
 *   rows: array<int, array{label: string, value: string|array<int, string>}>
 * }> $modules
 * @var \Illuminate\Support\Collection $years
 * @var \Illuminate\Support\Collection $months
 * @var \Illuminate\Support\Collection $days
 */
?>

<x-app-layout :journal="$journal">
  <x-slot:title>
    {{ __('Journal') }}
  </x-slot>

  <!-- list of years -->
  @include('app.journal.entry.partials.years', ['years' => $years])

  <!-- list of months -->
  @include('app.journal.entry.partials.months', ['months' => $months])

  <!-- list of days -->
  @include('app.journal.entry.partials.days', ['days' => $days])

  <!-- lock status -->
  @if (! $entry->isEditable())
    <div class="border-b border-gray-200 bg-gradient-to-r from-amber-50 to-orange-50 px-4 py-4 dark:border-gray-700 dark:from-amber-900/20 dark:to-orange-900/20">
      <div class="mx-auto max-w-3xl">
        <div class="flex items-center gap-3">
          <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/40">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-amber-600 dark:text-amber-400" viewBox="0 0 20 20" fill="currentColor">
              <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
            </svg>
          </div>
          <div class="flex-1">
            <h3 class="text-sm font-semibold text-amber-900 dark:text-amber-200">{{ __('This entry is locked') }}</h3>
            <p class="mt-0.5 text-xs text-amber-700 dark:text-amber-300">{{ __('Entries older than 48 hours cannot be modified to preserve the authenticity of your journal.') }}</p>
          </div>
        </div>
      </div>
    </div>
  @endif

  <div class="w-5xl mx-auto px-4 py-8 sm:px-8">
    <div class="relative h-full w-full overflow-hidden bg-[#fdf9f0] text-gray-900 shadow-[0_1px_3px_rgba(0,0,0,0.04),0_8px_20px_rgba(0,0,0,0.06)] ring-1 ring-black/5 before:pointer-events-none before:absolute before:inset-0 before:bg-[linear-gradient(transparent_1.45rem,_rgba(13,13,13,0.06)_1.45rem,_rgba(13,13,13,0.06)_1.5rem)] before:bg-[length:100%_1.5rem] after:pointer-events-none after:absolute after:inset-y-0 after:left-8 after:w-px after:bg-red-300/70 dark:bg-slate-900 dark:text-gray-100 dark:ring-white/10 dark:before:bg-[linear-gradient(transparent_1.45rem,_rgba(255,255,255,0.08)_1.45rem,_rgba(255,255,255,0.08)_1.5rem)] dark:after:bg-red-400/40">
      <div class="relative z-10 space-y-6 px-8 py-[0.25rem] text-left leading-6 sm:px-8">
        <div class="prose prose-slate dark:prose-invert relative mt-12 max-w-none leading-6">
          <p class="mb-6 font-bold">{{ __('Entry for :date', ['date' => $entryDate]) }}</p>

          @if ($notesMarkdown)
            <div class="space-y-2">
              <p class="flex items-center gap-x-2 font-semibold">
                <span>📝</span>
                {{ __('Notes') }}
              </p>
              <div class="prose prose-slate dark:prose-invert max-w-none">
                {!! $notesMarkdown !!}
              </div>
            </div>
          @endif

          @forelse ($modules as $module)
            <div class="space-y-1">
              <p class="flex items-center gap-x-2 font-semibold">
                <span>{{ $module['emoji'] }}</span>
                {{ $module['title'] }}
              </p>

              <div class="space-y-1 pl-1 text-sm">
                @foreach ($module['rows'] as $row)
                  @if (is_array($row['value']))
                    <div class="space-y-1">
                      <span class="font-medium text-gray-600 dark:text-gray-300">{{ $row['label'] }}:</span>
                      <ul class="list-disc space-y-0.5 pl-5">
                        @foreach ($row['value'] as $item)
                          <li>{{ $item }}</li>
                        @endforeach
                      </ul>
                    </div>
                  @else
                    <div class="flex flex-wrap gap-x-2">
                      <span class="font-medium text-gray-600 dark:text-gray-300">{{ $row['label'] }}:</span>
                      <span>{{ $row['value'] }}</span>
                    </div>
                  @endif
                @endforeach
              </div>
            </div>
          @empty
            <p class="text-sm text-gray-600 dark:text-gray-300">{{ __('No modules were filled for this entry yet.') }}</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
