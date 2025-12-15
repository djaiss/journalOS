@if (Auth::user()->isInTrial())
  <div class="relative mx-auto flex w-full flex-wrap items-center justify-center gap-4 bg-linear-to-r from-amber-100 via-yellow-50 to-white px-4 py-2 text-center ring-1 ring-amber-200/70 transition duration-150 sm:flex-nowrap sm:justify-center sm:text-left dark:from-yellow-900/40 dark:via-amber-900/20 dark:to-gray-900 dark:ring-amber-700/40" x-data="{ showTooltip: false }" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false">
    <div class="flex items-center gap-3">
      <span class="flex size-9 items-center justify-center rounded-lg bg-amber-500/80 text-amber-950 shadow-inner ring-1 ring-white/70 dark:bg-amber-400/80">
        <x-phosphor-hourglass class="size-5" />
      </span>

      <div class="flex flex-col leading-tight">
        <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">
          {{ __(':days days left in your trial', ['days' => round(now()->diffInDays(Auth::user()->trial_ends_at))]) }}
        </p>
        <p class="text-xs text-amber-800/80 dark:text-amber-100/80">
          {{ __('Unlock everything forever with a one-time fee.') }}
        </p>
      </div>
    </div>

    <a href="" class="inline-flex items-center gap-2 rounded-lg bg-amber-500 px-3 py-1.5 text-sm font-semibold text-amber-950 shadow-sm transition duration-150 hover:-translate-y-0.5 hover:bg-amber-400 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-amber-500/70 dark:bg-amber-400 dark:text-amber-950">
      <x-phosphor-sparkle class="size-4" />
      {{ __('Unlock') }}
    </a>

    <div x-cloak x-show="showTooltip" x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="translate-y-1 opacity-0" x-transition:enter-end="translate-y-0 opacity-100" x-transition:leave="transition duration-150 ease-in" x-transition:leave-start="translate-y-0 opacity-100" x-transition:leave-end="translate-y-1 opacity-0" class="absolute top-full left-1/2 z-50 mt-3 flex w-88 -translate-x-1/2 items-center gap-4 rounded-xl bg-white p-4 shadow-lg ring-1 ring-black/5 dark:bg-gray-900 dark:ring-white/10">
      <x-image src="{{ asset('images/vandamme.webp') }}" alt="One-time fee" width="80" height="80" class="h-20 w-20 rounded-full object-cover shadow-sm" srcset="{{ asset('images/vandamme.webp') }} 1x, {{ asset('images/vandamme@2x.webp') }} 2x" />
      <div class="flex flex-col gap-1 text-sm text-gray-700 dark:text-gray-200">
        <p>{{ __("It's a one-time fee, and will unlock everything!") }}</p>
        <p>{{ __('No recurring charges. Keep your momentum rolling.') }}</p>
        <p class="font-semibold">{{ __('Van Damme would be proud of you.') }}</p>
      </div>
    </div>
  </div>
@endif

<header {{ $attributes->class(['flex w-full max-w-[1920px] items-center px-2 sm:pr-4 sm:pl-9']) }}>
  <!-- normal desktop header -->
  <nav class="hidden flex-1 items-center gap-3 pt-2 pb-2 sm:flex">
    <a href="/" class="flex items-center">
      <x-image src="{{ asset('logo/30x30.webp') }}" srcset="{{ asset('logo/30x30.webp') }} 1x, {{ asset('logo/30x30@2x.webp') }} 2x" width="20" height="20" alt="JournalOS logo" />
    </a>

    <!-- selectors -->
    @if (isset($journal))
      <div class="flex items-center gap-1">
        <a href="{{ route('journal.index') }}" data-turbo="true" class="rounded-md border border-transparent px-2 py-1 font-medium hover:border-gray-200 hover:bg-gray-100">{{ __('Dashboard') }}</a>
        <span class="text-gray-500">/</span>
        <div class="flex items-center gap-0">
          <a class="rounded-md border border-transparent px-2 py-1 font-medium hover:border-gray-200 hover:bg-gray-100">{{ $journal->name }}</a>
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

      <a href="{{ route('marketing.docs.index') }}" class="flex items-center gap-2 rounded-md border border-transparent px-2 py-1 font-medium hover:border-gray-200 hover:bg-gray-100">
        <x-phosphor-lifebuoy class="size-4 text-gray-600 transition-transform duration-150" />
        {{ __('Docs') }}
      </a>

      <div x-data="{ menuOpen: false }" @click.away="menuOpen = false" class="relative">
        <button @click="menuOpen = !menuOpen" :class="{ 'bg-gray-100' : menuOpen }" class="flex cursor-pointer items-center gap-1 rounded-md border border-transparent px-2 py-1 font-medium hover:border-gray-200 hover:bg-gray-100">
          {{ __('Menu') }}
          <x-phosphor-caret-down class="size-4 text-gray-600 transition-transform duration-150" x-bind:class="{ 'rotate-180' : menuOpen }" />
        </button>

        <div x-cloak x-show="menuOpen" x-transition:enter="transition duration-50 ease-linear" x-transition:enter-start="-translate-y-1 opacity-90" x-transition:enter-end="translate-y-0 opacity-100" class="absolute top-0 right-0 z-50 mt-10 w-56 min-w-32 rounded-md border border-gray-200/70 bg-white p-1 text-sm text-gray-800 shadow-md" x-cloak>
          @if (Auth::user()->is_instance_admin)
            <a @click="menuOpen = false" href="{{ route('instance.index') }}" class="relative flex w-full cursor-default items-center rounded px-2 py-1.5 outline-none select-none hover:bg-gray-100 hover:text-gray-900">
              <x-phosphor-user class="mr-2 size-4 text-gray-600" />
              {{ __('Instance administration') }}
            </a>

            <div class="-mx-1 my-1 h-px bg-gray-200"></div>
          @endif

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
      <x-image src="{{ asset('logo/30x30.webp') }}" srcset="{{ asset('logo/30x30.webp') }} 1x, {{ asset('logo/30x30@2x.webp') }} 2x" width="20" height="20" alt="JournalOS logo" />
    </a>

    <button @click="mobileMenuOpen = true" class="flex items-center gap-2 rounded-md border border-transparent py-1 font-medium hover:border-gray-200 hover:bg-gray-100">
      <x-phosphor-list class="size-5 text-gray-600 transition-transform duration-150" />
    </button>

    <!-- Mobile Menu Overlay -->
    <div x-cloak x-show="mobileMenuOpen" x-transition:enter="transition duration-50 ease-out" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition duration-50 ease-in" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 bg-white dark:bg-gray-900">
      <div class="flex h-full flex-col">
        <!-- Mobile Menu Header -->
        <div class="flex items-center justify-between border-b border-gray-200 px-2 py-1 dark:border-gray-700">
          <x-image src="{{ asset('logo/30x30.webp') }}" srcset="{{ asset('logo/30x30.webp') }} 1x, {{ asset('logo/30x30@2x.webp') }} 2x" width="20" height="20" alt="JournalOS logo" />

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
