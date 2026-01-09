<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    @include('components.meta')

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <x-rich-text::styles theme="richtextlaravel" data-turbo-track="false" />
  </head>
  <body class="flex min-h-screen flex-col font-sans text-sm text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100">
    <x-header :journal="$journal" />

    <main class="flex flex-1 flex-col bg-gray-50 px-2 py-px dark:bg-gray-950">
      <div class="mx-auto flex w-full grow flex-col items-stretch rounded-lg shadow-xs ring-1 ring-[#E6E7E9] dark:ring-gray-800">
        {{ $slot }}
      </div>
    </main>

    <x-footer />

    <x-toaster />
  </body>
</html>
