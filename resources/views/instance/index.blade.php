<x-app-layout>
  @include('instance.partials.banner')

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => __('Instance administration')]
  ]" />

  <div class="py-12">
    <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
      @include('instance.partials.menu')

      <!-- Stats -->
      <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <!-- Total accounts -->
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">{{ __('Total users') }}</p>
              <p class="text-2xl font-semibold">{{ $totalUsers }}</p>
            </div>
            <div class="rounded-sm bg-green-100 p-2">
              <x-phosphor-building class="h-5 w-5 text-green-600" />
            </div>
          </div>
        </div>

        <!-- New accounts last 30 days -->
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">{{ __('New users last 30 days') }}</p>
              <p class="text-2xl font-semibold">{{ $last30DaysUsers }}</p>
            </div>
            <div class="rounded-sm bg-blue-100 p-2">
              <x-phosphor-calendar class="h-5 w-5 text-blue-600" />
            </div>
          </div>
        </div>

        <!-- New accounts last 7 days -->
        <div class="rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-900">
          <div class="flex items-center justify-between">
            <div>
              <p class="text-sm text-gray-600">{{ __('New users last 7 days') }}</p>
              <p class="text-2xl font-semibold">{{ $last7DaysUsers }}</p>
            </div>
            <div class="rounded-sm bg-purple-100 p-2">
              <x-phosphor-trend-up class="h-5 w-5 text-purple-600" />
            </div>
          </div>
        </div>
      </div>

      <!-- Accounts list -->
      <div class="mb-8 overflow-hidden rounded-lg border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900" x-data="{
        search: '',
        users: {{ \Illuminate\Support\Js::from($users) }},
      }">
        <!-- Search -->
        <div class="border-b border-gray-200 p-4 dark:border-gray-700">
          <div class="flex items-center justify-end">
            <div class="relative w-full sm:w-64">
              <x-phosphor-magnifying-glass class="pointer-events-none absolute top-1/2 left-2 h-4 w-4 -translate-y-1/2 text-gray-500" />
              <x-input type="text" placeholder="{{ __('Search users...') }}" class="w-full border border-gray-300 bg-gray-100 py-1 pr-3 pl-8 text-sm focus:bg-white dark:border-gray-700 dark:bg-gray-800 dark:focus:bg-gray-900" x-model="search" />
            </div>
          </div>
        </div>

        <!-- Table header -->
        <div class="hidden grid-cols-12 gap-4 border-b border-gray-200 bg-gray-50 p-4 text-sm font-semibold text-gray-600 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 sm:grid">
          <div class="col-span-1">{{ __('ID') }}</div>
          <div class="col-span-5">{{ __('Administrator') }}</div>
          <div class="col-span-3">{{ __('Last activity') }}</div>
          <div class="col-span-3">{{ __('Journals') }}</div>
        </div>

        <!-- Table body -->
        <div class="divide-y divide-gray-200">
          <template x-for="user in users" :key="user.id">
            <a :href="user.url" class="grid cursor-pointer grid-cols-1 gap-2 p-4 text-sm hover:bg-blue-50 dark:hover:bg-gray-800 sm:grid-cols-12 sm:gap-4 sm:p-3" x-show="
              search === '' ||
                user.name.toLowerCase().includes(search.toLowerCase()) ||
                user.email.toLowerCase().includes(search.toLowerCase()) ||
                user.id.toString().includes(search)
            ">
              <!-- Mobile labels + content -->
              <div class="col-span-1 flex items-center justify-between sm:block">
                <span class="font-semibold sm:hidden">{{ __('ID:') }}</span>
                <span class="font-mono" x-text="user.id"></span>
              </div>

              <div class="col-span-5">
                <div class="flex items-center gap-2">
                  <div class="h-6 w-6 rounded-full bg-gray-200 sm:h-8 sm:w-8">
                    <img class="h-8 w-8 rounded-full object-cover p-[0.1875rem] shadow-sm ring-1 ring-slate-900/10" :src="user.avatar" />
                  </div>
                  <div class="min-w-0 flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:gap-2">
                      <p class="truncate font-semibold" x-text="user.name"></p>
                      <p class="truncate text-gray-600" x-text="user.email"></p>
                      <p class="text-xs text-gray-500" x-text="`ID: ${user.id}`"></p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-span-3 flex items-center justify-between sm:justify-start">
                <span class="font-semibold sm:hidden">{{ __('Last activity:') }}</span>
                <div class="flex items-center gap-2">
                  <x-phosphor-clock class="h-4 w-4 text-gray-500" />
                  <span x-text="user.last_activity_at"></span>
                </div>
              </div>

              <div class="col-span-3 flex items-center justify-between sm:justify-start">
                <span class="font-semibold sm:hidden">{{ __('Journals:') }}</span>
                <div class="flex items-center gap-2">
                  <x-phosphor-users class="h-4 w-4 text-gray-500" />
                  <span x-text="`${user.journals_count} journals`"></span>
                </div>
              </div>
            </a>
          </template>

          <!-- Empty state -->
          <div class="p-8 text-center" x-show="
            ! users.some(
              (user) =>
                user.name.toLowerCase().includes(search.toLowerCase()) ||
                user.email.toLowerCase().includes(search.toLowerCase()) ||
                user.id.toString().includes(search),
            )
          ">
            <x-phosphor-magnifying-glass class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">{{ __('No results found') }}</h3>
            <p class="mt-1 text-sm text-gray-500">{{ __('We could not find anything with that term. Try searching for something else.') }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
