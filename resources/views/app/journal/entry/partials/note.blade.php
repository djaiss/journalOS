<div class="relative h-full w-full overflow-hidden bg-[#fdf9f0] text-gray-900 shadow-[0_2px_8px_rgba(0,0,0,0.08),0_22px_60px_rgba(0,0,0,0.18)] ring-1 ring-black/5 before:pointer-events-none before:absolute before:inset-0 before:bg-[linear-gradient(transparent_1.45rem,_rgba(13,13,13,0.06)_1.45rem,_rgba(13,13,13,0.06)_1.5rem)] before:bg-[length:100%_1.5rem] after:pointer-events-none after:absolute after:inset-y-0 after:left-8 after:w-px after:bg-red-300/70 dark:bg-slate-900 dark:text-gray-100 dark:ring-white/10 dark:before:bg-[linear-gradient(transparent_1.45rem,_rgba(255,255,255,0.08)_1.45rem,_rgba(255,255,255,0.08)_1.5rem)] dark:after:bg-red-400/40">
  <div class="relative z-10 space-y-6 px-8 py-[0.25rem] text-left leading-6 sm:px-8">
    @if (! $module['display_reset'])
      <div class="mt-12 text-center">
        <p class="mb-4 text-sm leading-6 text-gray-700 dark:text-gray-300">
          {{ __('Add a short note to capture anything that doesn\'t fit elsewhere') }}
        </p>

        <x-button.secondary href="{{ $module['notes_edit_url'] }}" turbo="true">
          {{ __('Add a note') }}
        </x-button.secondary>
      </div>
    @else
      <div class="prose prose-slate dark:prose-invert mt-6 max-w-none leading-6">
        {!! $module['notes_markdown'] !!}
      </div>
    @endif
  </div>
</div>
