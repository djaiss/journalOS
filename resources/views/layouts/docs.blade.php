<?php
/**
 * @var array<int, array<string, mixed>> $breadcrumbItems
 * @var \Illuminate\View\ComponentSlot $slot
 */
?>

@props([
  'marketingPage',
  'breadcrumbItems' => [],
])

<x-marketing-layout>
  @if (! empty($breadcrumbItems))
    <x-breadcrumb :items="$breadcrumbItems" />
  @endif

  <div class="relative mx-auto max-w-7xl px-6 lg:px-8 xl:px-0">
    <div class="grid grid-cols-1 gap-x-16 lg:grid-cols-[250px_1fr]">
      <!-- Sidebar -->
      <div class="hidden w-full flex-shrink-0 flex-col justify-self-end sm:border-r sm:border-gray-200 sm:pr-3 lg:flex dark:sm:border-gray-700">
        <div
          x-data="{
            conceptsDocumentation:
              '{{ str_starts_with( request()->route()->getName(),'marketing.docs.concepts.',) ? 'true' : 'false' }}' ===
              'true',
            openApiDocumentation:
              '{{ str_starts_with( request()->route()->getName(),'marketing.docs.api.',) ? 'true' : 'false' }}' ===
              'true',
            accountManagementDocumentation:
              '{{ str_starts_with( request()->route()->getName(),'marketing.docs.api.account',) ? 'true' : 'false' }}' ===
              'true',
            journalsDocumentation:
              '{{ str_starts_with( request()->route()->getName(),'marketing.docs.api.journals',) || str_starts_with( request()->route()->getName(),'marketing.docs.api.journal-entries',) ? 'true' : 'false' }}' ===
              'true',
            modulesDocumentation:
              '{{ str_starts_with( request()->route()->getName(),'marketing.docs.api.modules',) ? 'true' : 'false' }}' ===
              'true',
          }"
          class="bg-light dark:bg-dark z-10 pt-16">
          <!-- concepts -->
          <div @click="conceptsDocumentation = !conceptsDocumentation" class="mb-2 flex cursor-pointer items-center justify-between rounded-md border border-transparent px-2 py-1 hover:border-gray-200 hover:bg-blue-50 dark:hover:border-gray-700 dark:hover:bg-gray-800">
            <h3>Concepts</h3>
            <x-phosphor-caret-right x-bind:class="conceptsDocumentation ? 'rotate-90' : ''" class="h-4 w-4 text-gray-500 transition-transform duration-300" />
          </div>

          <!-- concepts sub menu -->
          <div x-show="conceptsDocumentation" x-cloak class="mb-2 ml-3">
            <div class="mb-3 flex flex-col gap-y-2">
              <div>
                <a href="{{ route('marketing.docs.concepts.hierarchical-structure') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.concepts.hierarchical-structure') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Hierarchical structure</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.concepts.permissions') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.concepts.permissions') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Permissions</a>
              </div>
            </div>
          </div>

          <!-- api documentation -->
          <div @click="openApiDocumentation = !openApiDocumentation" class="mb-2 flex cursor-pointer items-center justify-between rounded-md border border-transparent px-2 py-1 hover:border-gray-200 hover:bg-blue-50 dark:hover:border-gray-700 dark:hover:bg-gray-800">
            <h3>API documentation</h3>
            <x-phosphor-caret-right x-bind:class="openApiDocumentation ? 'rotate-90' : ''" class="h-4 w-4 text-gray-500 transition-transform duration-300" />
          </div>

          <!-- api documentation sub menu -->
          <div x-show="openApiDocumentation" x-cloak class="mb-10 ml-3">
            <div class="mb-3 flex flex-col gap-y-2">
              <div>
                <a href="{{ route('marketing.docs.api.index') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.index') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Introduction</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.authentication') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.authentication') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Authentication</a>
              </div>
              <div>
                <a href="" wire:navigate class="{{ request()->routeIs('marketing.docs.api.errors') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">HTTP status codes</a>
              </div>
            </div>

            <!-- account management -->
            <div @click="accountManagementDocumentation = !accountManagementDocumentation" class="mb-3 flex cursor-pointer items-center justify-between rounded-md border border-transparent px-2 py-1 pl-3 text-xs text-gray-500 uppercase hover:border-gray-200 hover:bg-blue-50 dark:text-gray-400 dark:hover:border-gray-700 dark:hover:bg-gray-800">
              <h3>Account management</h3>
              <x-phosphor-caret-right x-bind:class="accountManagementDocumentation ? 'rotate-90' : ''" class="h-4 w-4 text-gray-500 transition-transform duration-300" />
            </div>
            <div x-show="accountManagementDocumentation" class="mb-3 flex flex-col gap-y-2">
              <div>
                <a href="{{ route('marketing.docs.api.account.profile') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.account.profile') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Profile</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.account.logs') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.account.logs') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Logs</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.account.emails') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.account.emails') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Emails</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.account.api-management') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.account.api-management') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">API management</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.account') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.account') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Manage account</a>
              </div>
            </div>

            <!-- journals -->
            <div @click="journalsDocumentation = !journalsDocumentation" class="mb-3 flex cursor-pointer items-center justify-between rounded-md border border-transparent px-2 py-1 pl-3 text-xs text-gray-500 uppercase hover:border-gray-200 hover:bg-blue-50 dark:text-gray-400 dark:hover:border-gray-700 dark:hover:bg-gray-800">
              <h3>Journals</h3>
              <x-phosphor-caret-right x-bind:class="journalsDocumentation ? 'rotate-90' : ''" class="h-4 w-4 text-gray-500 transition-transform duration-300" />
            </div>
            <div x-show="journalsDocumentation" class="mb-3 flex flex-col gap-y-2">
              <div>
                <a href="{{ route('marketing.docs.api.journals') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.journals') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Journals</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.journal-entries') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.journal-entries') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Journal entries</a>
              </div>
            </div>

            <!-- modules -->
            <div @click="modulesDocumentation = !modulesDocumentation" class="mb-3 flex cursor-pointer items-center justify-between rounded-md border border-transparent px-2 py-1 pl-3 text-xs text-gray-500 uppercase hover:border-gray-200 hover:bg-blue-50 dark:text-gray-400 dark:hover:border-gray-700 dark:hover:bg-gray-800">
              <h3>Modules</h3>
              <x-phosphor-caret-right x-bind:class="modulesDocumentation ? 'rotate-90' : ''" class="h-4 w-4 text-gray-500 transition-transform duration-300" />
            </div>
            <div x-show="modulesDocumentation" class="mb-3 flex flex-col gap-y-2">
              <div>
                <a href="{{ route('marketing.docs.api.modules.day-type') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.modules.day-type') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Day type</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.modules.primary-obligation') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.modules.primary-obligation') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Primary obligation</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.modules.health') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.modules.health') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Health</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.modules.hygiene') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.modules.hygiene') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Hygiene</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.modules.meals') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.modules.meals') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Meals</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.modules.energy') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.modules.energy') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Energy</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.modules.cognitive-load') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.modules.cognitive-load') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Cognitive load</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.modules.social-density') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.modules.social-density') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Social density</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.modules.mood') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.modules.mood') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Mood</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.modules.physical-activity') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.modules.physical-activity') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Physical activity</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.modules.sexual-activity') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.modules.sexual-activity') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Sexual activity</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.modules.sleep') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.modules.sleep') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Sleep</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.modules.travel') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.modules.travel') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Travel</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.modules.weather') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.modules.weather') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Weather</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.modules.weather-influence') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.modules.weather-influence') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Weather influence</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.modules.shopping') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.modules.shopping') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Shopping</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.modules.kids') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.modules.kids') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Kids</a>
              </div>
              <div>
                <a href="{{ route('marketing.docs.api.modules.work') }}" wire:navigate class="{{ request()->routeIs('marketing.docs.api.modules.work') ? 'border-l-blue-400' : 'border-l-transparent' }} block border-l-3 pl-3 hover:border-l-blue-400 hover:underline">Work</a>
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
