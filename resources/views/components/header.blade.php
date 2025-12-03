<header {{ $attributes->class(['flex w-full max-w-[1920px] items-center px-2 sm:pr-4 sm:pl-9']) }}>
  <!-- normal desktop header -->
  <nav class="hidden flex-1 items-center gap-3 pt-2 pb-2 sm:flex">
    <a href="/" class="flex items-center">
      <x-logo width="20" height="20" />
    </a>

    <!-- selectors -->
    @if (isset($organization))
      <div class="flex items-center gap-1">
        <a href="{{ route('organization.index') }}" data-turbo="true" class="rounded-md border border-transparent px-2 py-1 font-medium hover:border-gray-200 hover:bg-gray-100">{{ __('Dashboard') }}</a>
        <span class="text-gray-500">/</span>
        <div class="flex items-center gap-0">
          <a class="rounded-md border border-transparent px-2 py-1 font-medium hover:border-gray-200 hover:bg-gray-100">{{ $organization->name }}</a>
          <div class="rounded-md border border-transparent px-1 py-1 font-medium hover:border-gray-200 hover:bg-gray-100">
            <x-phosphor-caret-up-down class="size-4 text-gray-600" />
          </div>
        </div>
      </div>
    @endif

    <!-- separator -->
    <div class="-ml-4 flex-1"></div>

    <!-- right side menu -->
    <div class="flex items-center gap-1">
      <a class="flex items-center gap-2 rounded-md border border-transparent px-2 py-1 font-medium hover:border-gray-200 hover:bg-gray-100" href="/">
        <x-phosphor-magnifying-glass class="size-4 text-gray-600 transition-transform duration-150" />
        {{ __('Search') }}
      </a>

      <a href="" class="flex items-center gap-2 rounded-md border border-transparent px-2 py-1 font-medium hover:border-gray-200 hover:bg-gray-100">
        <x-phosphor-lifebuoy class="size-4 text-gray-600 transition-transform duration-150" />
        {{ __('Docs') }}
      </a>

      <div x-data="{ menuOpen: false }" @click.away="menuOpen = false" class="relative">
        <button @click="menuOpen = !menuOpen" :class="{ 'bg-gray-100' : menuOpen }" class="flex cursor-pointer items-center gap-1 rounded-md border border-transparent px-2 py-1 font-medium hover:border-gray-200 hover:bg-gray-100">
          {{ __('Menu') }}
          <x-phosphor-caret-down class="size-4 text-gray-600 transition-transform duration-150" x-bind:class="{ 'rotate-180' : menuOpen }" />
        </button>

        <div x-cloak x-show="menuOpen" x-transition:enter="transition duration-50 ease-linear" x-transition:enter-start="-translate-y-1 opacity-90" x-transition:enter-end="translate-y-0 opacity-100" class="absolute top-0 right-0 z-50 mt-10 w-48 min-w-[8rem] rounded-md border border-gray-200/70 bg-white p-1 text-sm text-gray-800 shadow-md" x-cloak>
          <a @click="menuOpen = false" href="{{ route('settings.profile.index') }}" class="relative flex w-full cursor-default items-center rounded px-2 py-1.5 outline-none select-none hover:bg-gray-100 hover:text-gray-900">
            <x-phosphor-user class="mr-2 size-4 text-gray-600" />
            {{ __('Profile') }}
          </a>

          <div class="-mx-1 my-1 h-px bg-gray-200"></div>

          <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button @click="menuOpen = false" type="submit" class="relative flex w-full cursor-default items-center rounded px-2 py-1.5 outline-none select-none hover:bg-gray-100 hover:text-gray-900">
              <x-phosphor-sign-out class="mr-2 size-4 text-gray-600" />
              {{ __('Logout') }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </nav>

  <!-- mobile header -->
  <nav class="flex w-full items-center justify-between gap-3 pt-2 pb-2 sm:hidden" x-data="{ mobileMenuOpen: false }">
    <a href="/">
      <x-logo width="20" height="20" />
    </a>

    <button @click="mobileMenuOpen = true" class="flex items-center gap-2 rounded-md border border-transparent py-1 font-medium hover:border-gray-200 hover:bg-gray-100">
      <x-phosphor-list class="size-5 text-gray-600 transition-transform duration-150" />
    </button>

    <!-- Mobile Menu Overlay -->
    <div x-cloak x-show="mobileMenuOpen" x-transition:enter="transition duration-50 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition duration-50 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 bg-white dark:bg-gray-900">
      <div class="flex h-full flex-col">
        <!-- Mobile Menu Header -->
        <div class="flex items-center justify-between border-b border-gray-200 px-2 py-1 dark:border-gray-700">
          <x-logo width="20" height="20" />

          <button @click="mobileMenuOpen = false" class="flex items-center gap-2 rounded-md border border-transparent py-2 font-medium hover:border-gray-200 hover:bg-gray-100 dark:hover:border-gray-600 dark:hover:bg-gray-800">
            <x-phosphor-x class="size-5 text-gray-600 dark:text-gray-400" />
          </button>
        </div>

        <!-- Mobile Menu Content -->
        <div class="flex-1 space-y-4 p-4">
          <a @click="mobileMenuOpen = false" href="/" class="flex items-center gap-3 rounded-md p-3 text-lg font-medium text-gray-800 transition-colors hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">
            {{ __('Dashboard') }}
          </a>

          <a @click="mobileMenuOpen = false" href="/" class="flex items-center gap-3 rounded-md p-3 text-lg font-medium text-gray-800 transition-colors hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">
            <x-phosphor-magnifying-glass class="size-5 text-gray-600 dark:text-gray-400" />
            {{ __('Search') }}
          </a>

          <a @click="mobileMenuOpen = false" href="/" class="flex items-center gap-3 rounded-md p-3 text-lg font-medium text-gray-800 transition-colors hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">
            <x-phosphor-lifebuoy class="size-5 text-gray-600 dark:text-gray-400" />
            {{ __('Docs') }}
          </a>

          <a @click="mobileMenuOpen = false" href="{{ route('settings.profile.index') }}" class="flex items-center gap-3 rounded-md p-3 text-lg font-medium text-gray-800 transition-colors hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">
            <x-phosphor-user class="size-5 text-gray-600 dark:text-gray-400" />
            {{ __('Profile') }}
          </a>
        </div>

        <!-- Mobile Menu Footer -->
        <div class="border-t border-gray-200 p-4 dark:border-gray-700">
          <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <button @click="mobileMenuOpen = false" type="submit" class="flex w-full items-center gap-3 rounded-md p-3 text-lg font-medium text-gray-800 transition-colors hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-800">
              <x-phosphor-sign-out class="size-5 text-gray-600 dark:text-gray-400" />
              {{ __('Logout') }}
            </button>
          </form>
        </div>
      </div>
    </div>
  </nav>
</header>
