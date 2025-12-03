<x-marketing-layout :marketingPage="$marketingPage">
  <!-- breadcrumb -->
  <div class="border-b border-gray-200 py-3 text-sm">
    <div class="mx-auto flex max-w-7xl items-center gap-x-2 px-6 lg:px-8 xl:px-0">
      <a href="" class="text-blue-500 hover:underline">Home</a>
      <span class="text-gray-500">&gt;</span>
      <span class="text-gray-600">Documentation</span>
    </div>
  </div>

  <div class="relative mx-auto max-w-7xl px-6 lg:px-8 xl:px-0">
    <div class="grid grid-cols-1 gap-x-16 lg:grid-cols-[250px_1fr]">
      <!-- Sidebar -->
      <div class="hidden w-full flex-shrink-0 flex-col justify-self-end sm:border-r sm:border-gray-200 sm:pr-3 lg:flex">
        <div x-data="{
          conceptsDocumentation:
            '{{ str_starts_with( request()->route()->getName(),'marketing.docs.concepts.',) ? 'true' : 'false' }}' ===
            'true',
          openApiDocumentation:
            '{{ str_starts_with( request()->route()->getName(),'marketing.docs.api.',) ? 'true' : 'false' }}' ===
            'true',
          accountManagementDocumentation:
            '{{ str_starts_with( request()->route()->getName(),'marketing.docs.api.account.',) ? 'true' : 'false' }}' ===
            'true',
          organizationsDocumentation:
            '{{ str_starts_with( request()->route()->getName(),'marketing.docs.api.organizations',) ? 'true' : 'false' }}' ===
            'true',
        }" class="bg-light dark:bg-dark z-10 pt-16">
          <!-- concepts -->
          <div @click="conceptsDocumentation = !conceptsDocumentation" class="mb-2 flex cursor-pointer items-center justify-between rounded-md border border-transparent px-2 py-1 hover:border-gray-200 hover:bg-blue-50">
            <h3>Concepts</h3>
            <x-phosphor-caret-right x-bind:class="conceptsDocumentation ? 'rotate-90' : ''" class="h-4 w-4 text-gray-500 transition-transform duration-300" />
          </div>

          <!-- concepts sub menu -->
          <div x-show="conceptsDocumentation" x-cloak class="mb-2 ml-3">
            <div class="mb-3 flex flex-col gap-y-2">
              <div>
                <a href="{{ route('marketing.docs.concepts.hierarchical-structure') }}" class="{{ request()->routeIs('marketing.docs.concepts.hierarchical-structure') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Hierarchical structure</a>
              </div>
            </div>
          </div>

          <!-- api documentation -->
          <div @click="openApiDocumentation = !openApiDocumentation" class="mb-2 flex cursor-pointer items-center justify-between rounded-md border border-transparent px-2 py-1 hover:border-gray-200 hover:bg-blue-50">
            <h3>API documentation</h3>
            <x-phosphor-caret-right x-bind:class="openApiDocumentation ? 'rotate-90' : ''" class="h-4 w-4 text-gray-500 transition-transform duration-300" />
          </div>

          <!-- api documentation sub menu -->
          <div x-show="openApiDocumentation" x-cloak class="mb-10 ml-3">
            <div class="mb-3 flex flex-col gap-y-2">
              <div>
                <a href="{{ route('marketing.docs.api.index') }}" class="{{ request()->routeIs('marketing.docs.api.index') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Introduction</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.authentication') }}" class="{{ request()->routeIs('marketing.docs.api.authentication') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Authentication</a>
              </div>
              <div>
                <a href="" class="{{ request()->routeIs('marketing.docs.api.errors') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">HTTP status codes</a>
              </div>
            </div>

            <!-- account management -->
            <div @click="accountManagementDocumentation = !accountManagementDocumentation" class="mb-3 flex cursor-pointer items-center justify-between rounded-md border border-transparent px-2 py-1 pl-3 text-xs text-gray-500 uppercase hover:border-gray-200 hover:bg-blue-50">
              <h3>Account management</h3>
              <x-phosphor-caret-right x-bind:class="accountManagementDocumentation ? 'rotate-90' : ''" class="h-4 w-4 text-gray-500 transition-transform duration-300" />
            </div>
            <div x-show="accountManagementDocumentation" class="mb-3 flex flex-col gap-y-2">
              <div>
                <a href="{{ route('marketing.docs.api.account.profile') }}" class="{{ request()->routeIs('marketing.docs.api.account.profile') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Profile</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.account.logs') }}" class="{{ request()->routeIs('marketing.docs.api.account.logs') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Logs</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.account.api-management') }}" class="{{ request()->routeIs('marketing.docs.api.account.api-management') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">API management</a>
              </div>
            </div>

            <!-- organizations -->
            <div @click="organizationsDocumentation = !organizationsDocumentation" class="mb-3 flex cursor-pointer items-center justify-between rounded-md border border-transparent px-2 py-1 pl-3 text-xs text-gray-500 uppercase hover:border-gray-200 hover:bg-blue-50">
              <h3>Organizations</h3>
              <x-phosphor-caret-right x-bind:class="organizationsDocumentation ? 'rotate-90' : ''" class="h-4 w-4 text-gray-500 transition-transform duration-300" />
            </div>
            <div x-show="organizationsDocumentation" class="mb-3 flex flex-col gap-y-2">
              <div>
                <a href="{{ route('marketing.docs.api.organizations') }}" class="{{ request()->routeIs('marketing.docs.api.organizations') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Organizations</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Main content -->
      {{ $slot }}
    </div>
  </div>
</x-marketing-layout>
