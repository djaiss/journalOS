<x-app-layout>
  <x-slot name="organizationContext">
    <div class="max-w-7xl mx-auto px-4 sm:px-0 py-2 ">
      <ul class="text-sm">
        <li class="inline after:content-['>'] after:text-gray-500 after:text-xs">
          <x-link href="{{ route('dashboard.index') }}">{{ __('Dashboard') }}</x-link>
        </li>
        <li class="inline" dusk="header-organization-name">{{ $data['organization']['name'] }}</li>
      </ul>
    </div>
  </x-slot>

  <x-slot name="navigation">
    @include('layouts.navigation')
  </x-slot>

  <div class="py-12">
    <div class="mx-auto max-w-7xl px-4 sm:px-0">
      <div class="settings-grid grid grid-cols-1 gap-6">
        <!-- left -->
        <div>
          @include('organization.adminland.partials.sidebar')
        </div>

        <!-- right -->
        <div class="p-0 sm:px-0 sm:py-0">
          <div class="max-w-7xl mx-auto sm:px-0 space-y-6">
            <div class="p-4 sm:p-4 bg-white dark:bg-gray-800 shadow rounded-lg">
              <section>
                <header class="mb-6">
                  <div class="flex justify-between items-center">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                      {{ __('Manage permissions in the organization') }}
                    </h2>

                    <x-primary-link href="{{ $data['url']['new_permission'] }}" class="text-sm">
                      <x-heroicon-c-plus class="w-4 h-4 mr-1" />
                      <span>{{ __('Create a permission') }}</span>
                    </x-primary-link>
                  </div>

                  <p class="max-w-xl mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ __("Permissions grant access for members. In addition to the available pre-defined permissions, you can create any custom permissions to fit your needs.") }}
                  </p>
                </header>

                <ul>
                  @foreach ($data['permissions'] as $permission)
                  <li class="flex items-center justify-between py-2 px-2 border-b last:border-b-0 border-gray-200 hover:bg-blue-50 dark:hover:bg-gray-600 hover:border-l-blue-300 hover:border-l-2 border-l-2 border-l-transparent">
                    <div>{{ $permission['label'] }} <span class="pl-2 text-sm text-gray-500">{{ trans_choice('{0} No action defined|[1,1] One action|[2,*] Many actions defined', count($permission['actions'])) }}</span></div>

                    <div x-data="{ dropdownOpen: false }" class="relative">
                      <x-heroicon-o-ellipsis-horizontal @click="dropdownOpen=true" class="w-5 h-5 text-gray-500 cursor-pointer" />

                      <div x-show="dropdownOpen"
                        @click.away="dropdownOpen=false"
                        x-transition:enter="ease-out duration-200"
                        x-transition:enter-start="-translate-y-2"
                        x-transition:enter-end="translate-y-0"
                        class="absolute top-0 z-50 w-56 mt-8 -translate-x-1/2 -right-28"
                        x-cloak>
                        <div class="p-1 mt-1 bg-white border rounded-md shadow-md border-neutral-200/70 text-neutral-700">

                          <div class="relative flex cursor-default select-none hover:bg-neutral-100 items-center rounded px-2 py-1.5 text-sm outline-none transition-colors data-[disabled]:pointer-events-none data-[disabled]:opacity-50">
                            <x-heroicon-o-pencil-square class="w-4 h-4 mr-2" />
                            <a href="{{ 'sdfs' }}" dusk="edit-channel-link">{{ __('Edit') }}</a>
                          </div>

                          <div class="h-px my-1 -mx-1 bg-neutral-200"></div>

                          <div class="text-red-700 relative flex cursor-default select-none hover:bg-neutral-100 items-center rounded px-2 py-1.5 text-sm outline-none transition-colors data-[disabled]:pointer-events-none data-[disabled]:opacity-50">
                            <x-heroicon-o-trash class="w-4 h-4 mr-2" />
                            <a href="{{ 'sdfs' }}" dusk="delete-channel-link">{{ __('Delete') }}</a>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                  @endforeach
                </ul>
              </section>
            </div>

            <div class="p-4 sm:p-4 bg-white dark:bg-gray-800 shadow rounded-lg">
              <div class="max-w-xl">
                sdf
              </div>
            </div>

            <div class="p-4 sm:p-4 bg-white dark:bg-gray-800 shadow rounded-lg">
              <div class="max-w-xl">
                sdsf
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-app-layout>
