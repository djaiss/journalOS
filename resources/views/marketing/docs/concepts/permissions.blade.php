<x-marketing-docs-layout>
  <div class="grid grid-cols-1 gap-x-16 lg:grid-cols-[1fr_250px]">
    <div class="py-16 sm:border-r sm:border-gray-200 sm:pr-10">
      <x-marketing.docs.h1 title="Permissions" />

      <p class="mb-2">{{ config('app.name') }} lets you define permissions to manage your organization at the most granular level. We currently support these three concepts that should let you manage everything the way you want:</p>
      <ul class="mb-2 list-disc pl-6">
        <li>Permissions,</li>
        <li>Roles,</li>
        <li>Groups.</li>
      </ul>

      <x-marketing.docs.h2 id="permissions" title="Permissions (the building blocks)" />

      <p class="mb-2">A permission is a single action someone can take.</p>

      <p class="mb-2">
        <strong>Example:</strong>
        <span class="font-mono text-sm">Create a project</span>
        ,
        <span class="font-mono text-sm">Update an organization profile</span>
        ,
        <span class="font-mono text-sm">View assets</span>
        .
      </p>

      <p class="mb-8">Permissions are very specific and form the foundation of the system.</p>

      <x-marketing.docs.h2 id="roles" title="Roles (bundles of permissions)" />

      <p class="mb-2">A role is a named set of permissions grouped together.</p>

      <p class="mb-2">
        <strong>Example roles:</strong>
        Admin → can manage projects, invite members, update the organization. Member → can create and edit their own projects. Viewer → can only look at things, not change them.
      </p>

      <p class="mb-10">Instead of handing out 10 separate permissions, you just give someone the “Admin” role.</p>

      <x-marketing.docs.h2 id="groups" title="Groups (collections of people)" />

      <p class="mb-2">A group is a collection of users inside your organization.</p>

      <p class="mb-2">
        <strong>Example:</strong>
        <span class="font-mono text-sm">Mobile Team</span>
        ,
        <span class="font-mono text-sm">Marketing Department</span>
        ,
        <span class="font-mono text-sm">All Employees</span>
      </p>

      <p class="mb-10">If a new hire joins Marketing, just add them to the group — they’ll instantly have the right access.</p>
    </div>

    <!-- Sidebar -->
    <div class="mb-10 hidden w-full flex-shrink-0 flex-col justify-self-end lg:flex">
      <div class="bg-light dark:bg-dark z-10 pt-16">
        <div class="mb-1 flex items-center justify-between">
          <p class="text-xs">Written by...</p>
        </div>
        <div class="-mx-4 pt-1">
          <a href="" class="border-light dark:border-dark text-primary dark:text-primary-dark hover:text-primary dark:hover:text-primary-dark relative flex justify-between rounded border hover:border-b-[4px] hover:transition-all active:top-[2px] active:border-b-1 md:mx-4">
            <div class="flex w-full flex-col justify-between gap-0.5 px-4 py-2">
              <h3 class="mb-0 text-base leading-tight"><span>Régis Freyd</span></h3>
              <p class="text-primary/50 dark:text-primary-dark/50 m-0 line-clamp-1 text-sm leading-tight">Project maintainer</p>
            </div>
            <div class="flex-shrink-0 px-4 py-2">
              <x-image src="{{ asset('marketing/regis.webp') }}" srcset="{{ asset('marketing/regis.webp') }}, {{ asset('marketing/regis@2x.webp') }} 2x" alt="Regis" width="48" height="48" class="h-12 w-12 rounded-full" />
            </div>
          </a>
        </div>
      </div>
      <div class="flex flex-grow items-end">
        <div class="sticky bottom-0 w-full">
          <!-- Table of Contents -->
          <div class="mb-4">
            <h4 class="mb-2 text-xs font-semibold text-gray-500 uppercase">Jump to</h4>
            <nav class="space-y-1 text-sm">
              <a href="#permissions" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white">Permissions</a>
              <a href="#roles" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white">Roles</a>
              <a href="#groups" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 text-gray-600 transition-colors duration-50 hover:border-gray-400 hover:bg-white">Groups</a>
            </nav>
          </div>
        </div>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
