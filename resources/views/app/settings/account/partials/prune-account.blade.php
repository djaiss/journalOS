<x-box>
  <x-slot:title>
    {{ __('Prune your account') }}
  </x-slot:title>

  <x-slot:description>
    {{ __('Delete all journals and related data from your account. This lets you start over with a new account.') }}
  </x-slot:description>

  <x-form onsubmit="return confirm('Are you absolutely sure? This action cannot be undone.');" action="{{ route('settings.account.prune') }}" method="put">
    <x-button.secondary type="submit" class="mr-2 text-sm">
      {{ __('Prune account') }}
    </x-button.secondary>
  </x-form>
</x-box>
