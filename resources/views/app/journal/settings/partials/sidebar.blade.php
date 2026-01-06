<aside class="flex-col border-b border-gray-200 bg-white px-4 py-4 sm:flex sm:rounded-bl-lg sm:border-r sm:border-b-0 dark:border-gray-700 dark:bg-gray-900">
  <nav class="flex flex-col gap-1">
    <p class="mb-1 text-xs font-medium text-gray-500 uppercase">{{ __('Journal') }}</p>
    <a data-turbo="true" href="{{ route('journal.settings.modules.index', ['slug' => $journal->slug]) }}" class="{{ request()->routeIs('journal.settings.modules.index') ? 'bg-gray-100 font-medium text-gray-900 dark:bg-gray-800 dark:text-gray-100' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' }} flex items-center gap-3 rounded-lg px-2 py-1">
      <x-phosphor-squares-four class="h-4 w-4 {{ request()->routeIs('journal.settings.modules.index') ? 'text-emerald-700' : 'text-gray-500' }}" />
      {{ __('Modules') }}
    </a>
    <a data-turbo="true" href="{{ route('journal.settings.management.index', ['slug' => $journal->slug]) }}" class="{{ request()->routeIs('journal.settings.management.index') ? 'bg-gray-100 font-medium text-gray-900 dark:bg-gray-800 dark:text-gray-100' : 'text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' }} flex items-center gap-3 rounded-lg px-2 py-1">
      <x-phosphor-wrench class="h-4 w-4 {{ request()->routeIs('journal.settings.management.index') ? 'text-emerald-700' : 'text-gray-500' }}" />
      {{ __('Maintenance') }}
    </a>
  </nav>
</aside>
