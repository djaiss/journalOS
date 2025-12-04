<div class="w-full" x-data="{ mobileMenuOpen: false }">
  <!-- main nav -->
  <nav class="max-w-8xl mx-auto flex h-12 items-center justify-between border-b border-gray-300 bg-zinc-100 px-3 sm:px-6 dark:border-slate-600 dark:bg-gray-800 dark:text-slate-200">
    <!-- Logo -->
    <div class="flex items-center">
      <a href="{{ route('marketing.index') }}" class="group flex items-center gap-x-2 transition-transform ease-in-out">
        <div class="transition-all duration-400 group-hover:-translate-y-0.5 group-hover:-rotate-3">
          <x-logo width="25" height="25" />
        </div>
        <span class="text-lg font-semibold text-gray-900 dark:text-white">{{ config('app.name') }}</span>
      </a>
    </div>

    <!-- Mobile menu button -->
    <div class="flex lg:hidden">
      <button type="button" @click="mobileMenuOpen = !mobileMenuOpen" class="inline-flex items-center justify-center rounded-md p-2 text-gray-700">
        <span class="sr-only">Open main menu</span>
        <x-phosphor-list class="h-6 w-6" x-show="!mobileMenuOpen" />
        <x-phosphor-x class="h-6 w-6" x-show="mobileMenuOpen" />
      </button>
    </div>

    <!-- Main navigation - centered (hidden on mobile) -->
    <div class="hidden flex-1 justify-center lg:flex">
      <div class="flex items-center gap-x-2">
        <a href="" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 transition-colors duration-150 hover:border-gray-400 hover:bg-white">
          <x-phosphor-question class="h-4 w-4 text-blue-600 group-hover:text-blue-700" />
          <p class="text-sm text-gray-700 group-hover:text-gray-900">Why {{ config('app.name') }}</p>
        </a>

        <a href="" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 transition-colors duration-150 hover:border-gray-400 hover:bg-white">
          <x-phosphor-squares-four class="h-4 w-4 text-purple-600 group-hover:text-purple-700" />
          <p class="text-sm text-gray-700 group-hover:text-gray-900">Features</p>
        </a>

        <a href="" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 transition-colors duration-150 hover:border-gray-400 hover:bg-white">
          <x-phosphor-credit-card class="h-4 w-4 text-green-600 group-hover:text-green-700" />
          <p class="text-sm text-gray-700 group-hover:text-gray-900">Pricing</p>
        </a>

        <a href="{{ route('marketing.docs.index') }}" wire:navigate.hover class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 transition-colors duration-150 hover:border-gray-400 hover:bg-white">
          <x-phosphor-book-open class="h-4 w-4 text-amber-600 group-hover:text-amber-700" />
          <p class="text-sm text-gray-700 group-hover:text-gray-900">Docs</p>
        </a>

        <a href="" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 transition-colors duration-150 hover:border-gray-400 hover:bg-white">
          <x-phosphor-users class="h-4 w-4 text-rose-600 group-hover:text-rose-700" />
          <p class="text-sm text-gray-700 group-hover:text-gray-900">Community</p>
        </a>

        <a href="" class="group flex items-center gap-x-2 rounded-sm border border-b-3 border-transparent px-2 py-1 transition-colors duration-150 hover:border-gray-400 hover:bg-white">
          <x-phosphor-building class="h-4 w-4 text-indigo-600 group-hover:text-indigo-700" />
          <p class="text-sm text-gray-700 group-hover:text-gray-900">Company</p>
        </a>
      </div>
    </div>

    <!-- Right side - user menu -->
    <div class="flex items-center gap-x-5">
      <a href="" class="text-sm text-gray-700">Sign in</a>
      <a href="" class="rounded-md bg-blue-600 px-3.5 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-blue-600">Get started</a>
    </div>
  </nav>

  <!-- Mobile menu (off-canvas) -->
  <div x-show="mobileMenuOpen" class="lg:hidden" style="display: none">
    <div class="fixed inset-0 z-50"></div>
    <div class="fixed inset-y-0 right-0 z-50 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
      <!-- Add this button for closing -->
      <div class="mb-4 flex justify-end">
        <button @click="mobileMenuOpen = false" class="rounded-md p-2 text-gray-500 hover:bg-gray-100">
          <x-phosphor-x class="h-6 w-6" />
          <span class="sr-only">Close menu</span>
        </button>
      </div>

      <div class="flex flex-col gap-y-4">
        <a href="" class="flex items-center gap-x-2 py-2 text-base leading-7 font-semibold text-gray-900">Why {{ config('app.name') }}</a>
        <a href="" class="flex items-center gap-x-2 py-2 text-base leading-7 font-semibold text-gray-900">Features</a>
        <a href="" class="flex items-center gap-x-2 py-2 text-base leading-7 font-semibold text-gray-900">Pricing</a>
        <a href="" class="flex items-center gap-x-2 py-2 text-base leading-7 font-semibold text-gray-900">Docs</a>
        <a href="" class="flex items-center gap-x-2 py-2 text-base leading-7 font-semibold text-gray-900">Community</a>
        <a href="" class="flex items-center gap-x-2 py-2 text-base leading-7 font-semibold text-gray-900">Company</a>
      </div>
    </div>
  </div>
</div>
