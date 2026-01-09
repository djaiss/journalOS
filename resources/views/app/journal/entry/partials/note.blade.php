<div class="flex justify-center">

  @if (! $module['display_reset'])
  <div class="space-y-4 text-center">
    <p class="mt-16 px-6">{{ __('Add a short note to capture anything that doesn’t fit elsewhere') }}</p>

    <x-button.secondary href="{{ $module['notes_edit_url'] }}" turbo="true">
      {{ __('Add a note') }}
    </x-button.secondary>
  </div>
  @else
  <div class="prose">{!! $module['notes_markdown'] !!}</div>
  @endif
</div>
