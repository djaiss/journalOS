<div id="tokens-index">
  @forelse ($data['tokens'] as $token)
  <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between hover:bg-blue-50 dark:hover:bg-gray-600 hover:border-l-blue-300 hover:border-l-2 border border-l-2 last:border-b-0 border-transparent border-b-gray-200 sm:border-b-0 sm:px-2 py-2">
    <div class="mb-2 sm:mb-0 flex items-center">
      <x-heroicon-o-key class="w-4 h-4 mr-1 text-gray-400 dark:text-gray-500" />
      <span class="font-mono text-sm">{{ $token['name'] }}</span>
    </div>

    <!-- actions -->
    <div class="text-sm flex">
      <div class="mr-2 text-gray-400">{{ $token['last_used'] }}</div>

      <div>
        <span
          dusk="cta-revoke-key-{{ $token['id'] }}"
          hx-delete="{{ route('settings.api.destroy', $token['id']) }}"
          hx-confirm="{{ __('Are you sure you want to proceed? This can not be undone.') }}"
          hx-headers='{"X-CSRF-TOKEN": "{{ csrf_token() }}"}'
          class="text-red-600 underline hover:no-underline cursor-pointer">{{ __('Revoke') }}</span>
      </div>
    </div>
  </div>
  @empty
  <x-primary-link href="{{ route('settings.api.new') }}" dusk="blank-cta-add-key">
    <x-heroicon-c-plus class="w-4 h-4 mr-1" />
    <span>{{ __('Add your first key') }}</span>
  </x-primary-link>
  @endforelse
</div>
