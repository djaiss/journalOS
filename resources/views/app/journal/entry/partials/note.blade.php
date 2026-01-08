<div class="flex justify-center">

  <div class="space-y-4 text-center">
    <p class="mt-16 px-6">{{ __('Add a short note to capture anything that doesn’t fit elsewhere') }}</p>

    <x-button.secondary href="{{ route('journal.index') }}" turbo="true">
      {{ __('Add a note') }}
    </x-button.secondary>

    <x-trix-input id="notes" name="notes" class="block w-full" :value="old('notes', $entry->notes?->toTrixHtml())" autocomplete="off" />
</div>
