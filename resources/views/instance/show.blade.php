<x-app-layout>
  @include('instance.partials.banner')

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => __('Instance administration'), 'route' => route('instance.index')],
    ['label' => __('Account details')],
  ]" />

  <x-slot name="header">
    <div class="flex items-center gap-x-3">
      <x-phosphor-caret-left class="h-5 w-5 text-gray-400" />
      <h2 class="text-xl leading-tight font-semibold text-gray-800 dark:text-gray-200">
        {{ __('Account details') }}
      </h2>
    </div>
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      <!-- Account Information -->
      <div class="mb-6 overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
        <div class="flex items-center justify-between border-b border-gray-200 p-3 dark:border-gray-700">
          <span class="text-xs font-medium">{{ __('Account ID') }}</span>
          <span class="font-mono text-lg text-gray-900 dark:text-gray-100">{{ $user->id }}</span>
        </div>
        <div class="grid grid-cols-4 divide-x divide-gray-200 dark:divide-gray-700">
          <!-- Created date -->
          <div class="p-6">
            <div class="flex flex-col gap-2">
              <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                <x-phosphor-calendar class="h-4 w-4" />
                <span class="text-xs font-medium">{{ __('Created On') }}</span>
              </div>
              <span class="font-mono text-gray-900 dark:text-gray-100">{{ $user->created_at->format('F d, Y') }}</span>
            </div>
          </div>

          <!-- Account owner -->
          <div class="p-6">
            <div class="flex flex-col gap-3">
              <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                <x-phosphor-user class="h-4 w-4" />
                <span class="text-xs font-medium">{{ __('Account owner') }}</span>
              </div>
              <div class="flex items-center gap-3">
                <div>
                  <p class="font-medium text-gray-900 dark:text-gray-100">{{ $user->getFullName() }}</p>
                  <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Last active -->
          <div class="p-6">
            <div class="flex flex-col gap-2">
              <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                <x-phosphor-building class="h-4 w-4" />
                <span class="text-xs font-medium">{{ __('Last active date') }}</span>
              </div>
              <span class="font-mono text-gray-900 dark:text-gray-100">{{ $user->last_activity_at->format('F d, Y') }}</span>
            </div>
          </div>

          <!-- Account status -->
          <div class="p-6">
            <div class="flex flex-col gap-3">
              <div class="flex items-center gap-2 text-gray-600 dark:text-gray-400">
                <x-phosphor-shield class="h-4 w-4" />
                <span class="text-xs font-medium">{{ __('Account status') }}</span>
              </div>
              @if ($user->has_lifetime_access)
                <div>
                  <div class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-3 py-1 dark:bg-green-900/40">
                    <x-phosphor-check-circle class="h-4 w-4 text-green-600" />
                    <span class="text-sm font-medium text-green-600 dark:text-green-300">{{ __('Paid account') }}</span>
                  </div>
                </div>
              @elseif ($user->is_instance_admin)
                <div>
                  <div class="inline-flex items-center gap-1.5 rounded-full bg-green-100 px-3 py-1 dark:bg-green-900/40">
                    <x-phosphor-check-circle class="h-4 w-4 text-green-600" />
                    <span class="text-sm font-medium text-green-600 dark:text-green-300">{{ __('Instance administrator') }}</span>
                  </div>
                </div>
              @else
                <div class="space-y-1.5">
                  <div class="inline-flex items-center gap-1.5 rounded-full bg-yellow-100 px-3 py-1 dark:bg-yellow-900/40">
                    <x-phosphor-clock class="h-4 w-4 text-yellow-600" />
                    <span class="text-sm font-medium text-yellow-600 dark:text-yellow-300">{{ __('Trial account') }}</span>
                  </div>
                  <p class="text-sm text-gray-600 dark:text-gray-400">{{ $user->trial_ends_at?->diffInDays() }} {{ __('days remaining') }}</p>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>

      <!-- Action pane -->
      @if (! $user->is_instance_admin && $user->id !== Auth::id())
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">
          <!-- Delete account -->
          <x-form onsubmit="return confirm('Are you absolutely sure? This action cannot be undone.');" action="{{ route('instance.destroy', $user) }}" method="delete" class="w-full">
            <button type="submit" class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg border border-red-200 bg-white px-4 py-3 text-sm font-medium text-red-600 shadow-xs transition hover:bg-red-50 dark:border-red-700/60 dark:bg-gray-900 dark:text-red-300 dark:hover:bg-gray-800">
              <x-phosphor-trash class="h-4 w-4" />
              {{ __('Delete account') }}
            </button>
          </x-form>

          <!-- Give free account -->
          @if (! $user->has_lifetime_access)
            <x-form onsubmit="return confirm('Are you absolutely sure? This action cannot be undone.');" action="{{ route('instance.users.free', $user) }}" method="put" class="w-full">
              <button type="submit" class="flex w-full cursor-pointer items-center justify-center gap-2 rounded-lg border border-green-200 bg-white px-4 py-3 text-sm font-medium text-green-600 shadow-xs transition hover:bg-green-50 dark:border-green-700/60 dark:bg-gray-900 dark:text-green-300 dark:hover:bg-gray-800">
                <x-phosphor-gift class="h-4 w-4" />
                {{ __('Give free account') }}
              </button>
            </x-form>
          @endif
        </div>
      @else
        <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-900">
          <div class="flex items-center gap-x-3">
            <x-phosphor-shield-check class="h-5 w-5 text-gray-400 dark:text-gray-500" />
            <p class="text-sm text-gray-600 dark:text-gray-400">
              {{ __('No actions can be taken on this account as it belongs to an instance administrator or yourself.') }}
            </p>
          </div>
        </div>
      @endif

      <!-- Latest actions -->
      <div class="overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
        <div class="border-b border-gray-200 px-4 py-3 dark:border-gray-700">
          <h3 class="text-lg font-semibold">{{ __('Latest actions') }}</h3>
        </div>

        <div class="divide-y divide-gray-200 dark:divide-gray-700">
          @foreach ($logs as $log)
            <div class="flex items-center justify-between p-4">
              <div class="flex items-center gap-3">
                <x-phosphor-pulse class="h-4 w-4 text-gray-500 dark:text-gray-400" />
                <div>
                  <p class="flex items-center gap-1 text-sm">
                    <span class="font-semibold">{{ $log->user->getFullName() }}</span>
                    <span class="text-gray-500 dark:text-gray-400">|</span>
                    <span class="font-mono text-xs">{{ $log->action }}</span>
                  </p>
                </div>
              </div>
              <p class="font-mono text-xs text-gray-500 dark:text-gray-400">{{ $log->created_at->diffForHumans() }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
