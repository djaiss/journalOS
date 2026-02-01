<?php
/**
 * No view data.
 */
?>

{{-- @llms-title: Homepage --}}
{{-- @llms-description: Welcome page for JournalOS. --}}
{{-- @llms-route: / --}}
<x-marketing-layout>
  @section('json-ld')
    <script type="application/ld+json">
      {
        "@@context": "https://schema.org",
        "@@type": "SoftwareApplication",
        "name": "JournalOS",
        "url": "https://journalos.cloud",
        "applicationCategory": "BusinessApplication",
        "operatingSystem": "All",
        "logo": "https://journalos.cloud/images/marketing/logo-full.png",
        "softwareHelp": "https://journalos.cloud/docs",
        "offers": {
          "@@type": "Offer",
          "price": "50.00",
          "priceCurrency": "USD",
          "availability": "https://schema.org/InStock"
        },
        "creator": {
          "@@type": "Organization",
          "name": "JournalOS",
          "url": "https://journalos.cloud",
          "logo": "https://journalos.cloud/images/marketing/logo-full.png"
        },
        "description": "JournalOS is a simple diary that helps you track daily life with quick, structured entries.",
        "inLanguage": "en",
        "screenshot": "https://journalos.cloud/images/screenshot.png"
      }
    </script>
  @endsection

  <!-- Hero Section -->
  <div class="relative bg-white dark:bg-gray-900">
    <div class="mx-auto max-w-7xl px-6 py-8 text-center sm:pt-20 sm:pb-8 lg:px-8 xl:px-0">
      <h1 class="text-4xl font-semibold tracking-tight text-gray-900 sm:text-6xl dark:text-gray-100">A simple diary to track the shape of your days.</h1>
      <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-400">JournalOS is a bullet journal that helps you log sleep, mood, health, work, and more without writing long essays.</p>
      <div class="mt-10 mb-5 flex items-center justify-center gap-x-6">
        <a href="{{ route('register') }}" class="rounded-md bg-blue-600 px-3.5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-blue-600">Get started for free</a>
        <x-tooltip text="Just kidding. We have no sales teams.">
          <p class="cursor-pointer text-sm leading-6 font-semibold text-gray-900 dark:text-gray-100">
            Talk to sales
            <span aria-hidden="true">→</span>
          </p>
        </x-tooltip>
      </div>

      <p>
        You can also try <a href="{{ route('demo.index') }}" data-turbo="true" class="text-blue-600 hover:underline">a demo journal</a> if you see how the product works without creating an account.
      </p>
    </div>
  </div>

  <!-- App section -->
  @include('marketing.partials.app')

  <div class="flex justify-center md:hidden">
    <x-image class="mb-10" src="{{ asset('images/marketing/app.webp') }}" srcset="{{ asset('images/marketing/app.webp') }} 1x, {{ asset('images/marketing/app@2x.webp') }} 2x" width="378" height="471" alt="JournalOS main user interface" />
  </div>

  <!-- Feature section -->
  <div id="features" class="bg-gray-50 py-12 dark:bg-gray-800">
    <div class="mx-auto max-w-7xl px-6 lg:px-8 xl:px-0">
      <!-- Title -->
      <div class="mx-auto max-w-2xl lg:text-center mb-10">
        <h2 class="text-base leading-7 font-semibold text-green-600">Your personal diary</h2>
        <p class="mt-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-gray-100">Organize your journal the way you want to.</p>
        <p class="mt-4 text-lg leading-8 text-gray-600 dark:text-gray-400">Each journal entry can have a layout that you define.</p>
      </div>

      <!-- App section -->
      @include('marketing.partials.modules')

      <!-- 3 benefits -->
      <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:max-w-none">
        <dl class="mb-20 grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
          <!-- Daily logging -->
          <div class="flex flex-col">
            <dt class="flex items-center gap-x-3 text-base leading-7 font-semibold text-gray-900 dark:text-gray-100">
              <x-phosphor-shield-check class="h-5 w-5 flex-none text-blue-600" />
              Daily logging
            </dt>
            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-400">
              <p class="flex-auto">Track your day in minutes with structured prompts like sleep, mood, health, work, and more.</p>
            </dd>
          </div>

          <!-- No long essays -->
          <div class="flex flex-col">
            <dt class="flex items-center gap-x-3 text-base leading-7 font-semibold text-gray-900 dark:text-gray-100">
              <x-phosphor-sparkle class="h-5 w-5 flex-none text-blue-600" />
              No long essays required
            </dt>
            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-400">
              <p class="flex-auto">Capture the essentials quickly so journaling stays sustainable, even on busy days.</p>
            </dd>
          </div>

          <!-- Editing rules -->
          <div class="flex flex-col">
            <dt class="flex items-center gap-x-3 text-base leading-7 font-semibold text-gray-900 dark:text-gray-100">
              <x-phosphor-database class="h-5 w-5 flex-none text-blue-600" />
              Protect past entries
            </dt>
            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-400">
              <p class="flex-auto">Lock older entries so your record stays honest and consistent over time.</p>
            </dd>
          </div>
        </dl>

        <dl class="mb-20 grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-none lg:grid-cols-3">
          <!-- Stats -->
          <div class="flex flex-col">
            <dt class="flex items-center gap-x-3 text-base leading-7 font-semibold text-gray-900 dark:text-gray-100">
              <x-phosphor-warning class="h-5 w-5 flex-none text-blue-600" />
              Monthly and yearly stats
            </dt>
            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-400">
              <p class="flex-auto">See trends and patterns across weeks, months, and years with simple summaries.</p>
            </dd>
          </div>

          <!-- Reminders -->
          <div class="flex flex-col">
            <dt class="flex items-center gap-x-3 text-base leading-7 font-semibold text-gray-900 dark:text-gray-100">
              <x-phosphor-gift class="h-5 w-5 flex-none text-blue-600" />
              Random memories
            </dt>
            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-400">
              <p class="flex-auto">Get gentle email reminders of what happened in the past to bring moments back to life.</p>
            </dd>
          </div>

          <!-- Translations -->
          <div class="flex flex-col">
            <dt class="flex items-center gap-x-3 text-base leading-7 font-semibold text-gray-900 dark:text-gray-100">
              <x-phosphor-translate class="h-5 w-5 flex-none text-blue-600" />
              English and French
            </dt>
            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-400">
              <p class="flex-auto">Fully translated so your journal feels natural in the language you use every day.</p>
            </dd>
          </div>
        </dl>

        <div class="mx-auto text-center">
          <a href="{{ route('marketing.features.modules') }}" data-turbo="true" class="group mb-3 inline-flex items-center gap-x-2 rounded-sm border border-b-3 border-gray-400 bg-white px-3 py-2 transition-colors duration-150 hover:bg-white dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600">
            <x-phosphor-building class="h-4 w-4 text-indigo-600 group-hover:text-indigo-700" />
            <span class="text-sm text-gray-700 group-hover:text-gray-900 dark:text-gray-300 dark:group-hover:text-gray-100">See every module you can track</span>
          </a>
          <p class="text-sm text-gray-600 italic dark:text-gray-400">A diary that stays simple, fast, and genuinely useful.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- open source -->
  <div id="open-source" class="bg-white py-12 dark:bg-gray-900">
    <div class="mx-auto max-w-7xl px-6 lg:px-8 xl:px-0">
      <!-- Title -->
      <div class="mx-auto mb-10 max-w-2xl lg:text-center">
        <p class="mb-2 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-gray-100">JournalOS is proudly open source.</p>
        <h2 class="text-base leading-7 font-semibold text-green-600">Free to read. Free to modify. Free to own it yourself.</h2>
      </div>

      <div class="mx-auto mb-10 max-w-2xl lg:text-center">
        <div class="flex items-center justify-center gap-x-8">
          <a href="https://github.com/djaiss/journalos" target="_blank" class="group inline-flex items-center gap-x-2 rounded-sm border border-b-3 border-gray-400 bg-white px-3 py-2 transition-colors duration-150 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600">
            <x-phosphor-github-logo class="h-4 w-4 text-gray-600 dark:text-gray-300" />
            <span class="text-sm text-gray-700 group-hover:text-gray-900 dark:text-gray-300 dark:group-hover:text-gray-100">Read the code on GitHub</span>
          </a>

          <a href="https://github.com/djaiss/journalos" target="_blank" class="group inline-flex items-center gap-x-2 rounded-sm border border-b-3 border-gray-400 bg-white px-3 py-2 transition-colors duration-150 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600">
            <x-phosphor-star class="h-4 w-4 text-gray-600 dark:text-gray-300" />
            <span class="text-sm text-gray-700 group-hover:text-gray-900 dark:text-gray-300 dark:group-hover:text-gray-100">Stars</span>
            <span class="rounded-full bg-gray-100 px-2 py-0.5 font-mono text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-300">{{ $stars ?? 0 }}</span>
          </a>

          <a href="https://github.com/djaiss/journalos" target="_blank" class="group inline-flex items-center gap-x-2 rounded-sm border border-b-3 border-gray-400 bg-white px-3 py-2 transition-colors duration-150 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600">
            <x-phosphor-scales class="h-4 w-4 text-gray-600 dark:text-gray-300" />
            <span class="text-sm text-gray-700 group-hover:text-gray-900 dark:text-gray-300 dark:group-hover:text-gray-100">MIT licensed</span>
          </a>
        </div>
      </div>

      <div class="mx-auto mt-16 max-w-2xl sm:mt-20 lg:max-w-none">
        <dl class="mx-auto grid max-w-xl grid-cols-1 gap-x-8 gap-y-16 lg:max-w-4xl lg:grid-cols-2">
          <div class="flex flex-col">
            <dt class="flex items-center gap-x-3 text-base leading-7 font-semibold text-gray-900 dark:text-gray-100">
              <x-phosphor-clover class="h-5 w-5 flex-none text-blue-600" />
              Our code is public.
            </dt>
            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-400">
              <p class="flex-auto">
                Everyone can read
                <a href="https://github.com/djaiss/journalos" target="_blank" class="text-blue-500 hover:underline">our code</a>
                . Everyone can contribute. Everyone can change it. It's completely free to download, change and modify the software for your own use.
              </p>
            </dd>
          </div>

          <div class="flex flex-col">
            <dt class="flex items-center gap-x-3 text-base leading-7 font-semibold text-gray-900 dark:text-gray-100">
              <x-phosphor-sparkle class="h-5 w-5 flex-none text-blue-600" />
              MIT-licensed
            </dt>
            <dd class="mt-4 flex flex-auto flex-col text-base leading-7 text-gray-600 dark:text-gray-400">
              <p class="flex-auto">Our code is released under the MIT license. Perhaps it means nothing to you, but it's one of the most respected licenses out there. Most other tools say they are open source, but they are not truly.</p>
            </dd>
          </div>
        </dl>
      </div>
    </div>
  </div>

  <!-- why -->
  <div id="why" class="bg-gray-50 py-12 dark:bg-gray-800">
    <div class="mx-auto max-w-4xl rounded-lg bg-green-100 px-6 py-4 dark:bg-green-900/30">
      <div class="grid grid-cols-1 gap-x-8 gap-y-16 lg:grid-cols-3 lg:items-center">
        <div class="col-span-2">
          <h2 class="mb-3 text-xl leading-7 font-semibold text-green-600 dark:text-green-400">Why should you use JournalOS?</h2>
          <p class="mb-2 dark:text-gray-300">JournalOS is designed for people with busy lives who want a simple way to remember how their days felt. If you do not want to write long essays but still want to track your life, this is for you.</p>
          <p class="mb-2 dark:text-gray-300">We built it so you can capture the important details quickly and still look back with clarity.</p>
          <p class="dark:text-gray-300">
            Read more about
            <a href="" class="text-blue-500 hover:underline">why this tool exists</a>
            .
          </p>
        </div>
        <div class="hidden md:flex items-center lg:col-span-1 lg:items-start">
          <div class="flex flex-col" x-data="{ isRotating: false }">
            <div class="relative">
              <x-image src="{{ asset('images/marketing/regis.webp') }}" srcset="{{ asset('images/marketing/regis.webp') }} 1x, {{ asset('images/marketing/regis@2x.webp') }} 2x" alt="Monica" class="mb-3 w-40 rounded-lg transition-all duration-[2000ms] ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:scale-110 hover:rotate-[360deg] lg:rotate-4" @mouseenter="isRotating = true" @mouseleave="isRotating = false" @transitionend="isRotating = false" width="187" height="187" />

              <!-- Tooltip -->
              <div x-show="isRotating" x-transition.opacity class="bg-opacity-75 absolute top-1/2 right-full mr-3 -translate-y-1/2 rounded-lg bg-black px-3 py-2 text-sm whitespace-nowrap text-white">Please stooooop this! 😵‍💫</div>
            </div>
            <p class="text-xs text-gray-600 lg:rotate-4 dark:text-gray-400">Régis</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- CTA Section -->
  <div id="privacy" class="bg-gray-50 py-12 sm:py-20 dark:bg-gray-800">
    <div class="mx-auto max-w-7xl px-6 lg:px-8 xl:px-0">
      <h3 class="text-center mt-2 mb-3 text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-gray-100">Privacy and transparency are at the core of what we do.</h3>
      <p class="text-center mb-10 text-xl dark:text-gray-300">You are not our product. You are the reason we exist.</p>

      <div class="grid grid-cols-1 gap-x-8 gap-y-16 lg:grid-cols-4">
        <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-900/50">
          <div class="mb-2 flex justify-center">
            <x-phosphor-eye class="h-6 w-6 text-green-600" />
          </div>
          <h4 class="mb-3 text-center text-lg font-bold dark:text-gray-100">Transparent by nature</h4>
          <p class="dark:text-gray-400">Our code is open source, so you can see exactly how we build JournalOS.</p>
        </div>
        <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-900/50">
          <div class="mb-2 flex justify-center">
            <x-phosphor-lock class="h-6 w-6 text-green-600" />
          </div>
          <h4 class="mb-3 text-center text-lg font-bold dark:text-gray-100">Data is encrypted at rest</h4>
          <p class="dark:text-gray-400">We use industry-standard encryption to protect your data. If someone would steal the database, they would only see a bunch of gibberish.</p>
        </div>
        <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-900/50">
          <div class="mb-2 flex justify-center">
            <x-phosphor-eye-slash class="h-6 w-6 text-green-600" />
          </div>
          <h4 class="mb-3 text-center text-lg font-bold dark:text-gray-100">We do not track you</h4>
          <p class="dark:text-gray-400">There are no JavaScript trackers or ads on this website. We only measure page views to improve the site.</p>
        </div>
        <div class="rounded-lg bg-gray-50 p-4 dark:bg-gray-900/50">
          <div class="mb-2 flex justify-center">
            <x-phosphor-brain class="h-6 w-6 text-green-600" />
          </div>
          <h4 class="mb-3 text-center text-lg font-bold dark:text-gray-100">No AI shortcuts</h4>
          <p class="dark:text-gray-400">We do not rely on AI in the application. It is not ready for prime time and would create privacy issues.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- you will hate it -->
  <div id="hate" class="bg-white py-12 sm:py-20 dark:bg-gray-900">
    <div class="mx-auto max-w-7xl px-6 lg:px-8 xl:px-0">
      <div class="mx-auto mb-10 max-w-7xl">
        <h3 class="mt-2 mb-16 text-center text-3xl font-bold tracking-tight text-gray-900 sm:text-4xl dark:text-gray-100">
          Warning: JournalOS is probably
          <span class="rounded-md bg-amber-500 px-1 py-0 text-white sm:px-2 sm:py-1">not for you</span>
          if...
        </h3>

        <div class="grid grid-cols-1 gap-x-8 gap-y-16 lg:grid-cols-4">
          <div class="rotate-2 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-2 flex items-center justify-center">
              <x-image src="{{ asset('images/marketing/good_memory.webp') }}" srcset="{{ asset('images/marketing/good_memory.webp') }} 1x, {{ asset('images/marketing/good_memory@2x.webp') }} 2x" alt="Good memory" width="167" height="250" />
            </div>
            <p class="mb-3 text-xl dark:text-gray-100">You remember every detail</p>
            <p class="text-sm dark:text-gray-400">If your memory already captures every day, you probably do not need this tool.</p>
          </div>

          <div class="-rotate-1 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-2 flex items-center justify-center">
              <x-image src="{{ asset('images/marketing/recurring.webp') }}" srcset="{{ asset('images/marketing/recurring.webp') }} 1x, {{ asset('images/marketing/recurring@2x.webp') }} 2x" alt="Expensive subscriptions" width="167" height="250" />
            </div>
            <p class="mb-3 text-xl dark:text-gray-100">You like expensive, recurring subscriptions</p>
            <p class="text-sm dark:text-gray-400">We offer a one-time payment for the software. No subscriptions, no hidden fees.</p>
          </div>

          <div class="-rotate-1 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-2 flex items-center justify-center">
              <x-image src="{{ asset('images/marketing/ads.webp') }}" srcset="{{ asset('images/marketing/ads.webp') }} 1x, {{ asset('images/marketing/ads@2x.webp') }} 2x" alt="Ads" width="167" height="250" />
            </div>
            <p class="mb-3 text-xl dark:text-gray-100">You like being tracked for ads purposes</p>
            <p class="text-sm dark:text-gray-400">We do not track users to serve ads, and we do not profile our users.</p>
          </div>

          <div class="rotate-2 rounded-lg border border-gray-200 bg-white p-4 dark:border-gray-700 dark:bg-gray-800">
            <div class="mb-2 flex items-center justify-center">
              <x-image src="{{ asset('images/marketing/prison.webp') }}" srcset="{{ asset('images/marketing/prison.webp') }} 1x, {{ asset('images/marketing/prison@2x.webp') }} 2x" alt="Prison" width="167" height="250" />
            </div>
            <p class="mb-3 text-xl dark:text-gray-100">You like being locked in</p>
            <p class="text-sm dark:text-gray-400">We believe you should self-host on your own server if you can.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Testimonials -->
  <div class="bg-gray-50 dark:bg-gray-800">
    <div class="mx-auto max-w-7xl py-24 sm:px-6 sm:py-32 lg:px-0">
      <div class="relative isolate overflow-hidden bg-gray-900 px-6 py-24 text-center shadow-2xl sm:rounded-3xl sm:px-16">
        <h2 class="mx-auto max-w-2xl text-3xl font-bold tracking-tight text-white sm:text-4xl">Take control of your days and go out there. Life is not lived in front of a computer screen.</h2>
        <p class="mx-auto mt-6 max-w-xl text-lg leading-8 text-gray-300">But if you cannot, we can help with a simple tool to remember what matters.</p>
        <div class="mt-10 flex items-center justify-center gap-x-6">
          <a href="{{ route('register') }}" class="rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Get started for free</a>
          <a href="{{ route('login') }}" class="text-sm leading-6 font-semibold text-white">
            Sign in
            <span aria-hidden="true">→</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</x-marketing-layout>
