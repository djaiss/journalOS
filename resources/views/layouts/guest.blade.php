<?php
/**
 * @var \Illuminate\View\ComponentSlot $slot
 * @var string|null $title
 */
?>

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    @include('components.meta', ['title' => $title ?? null])

    @if (config('app.show_marketing_site'))
      <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endif

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
  </head>
  <body class="font-sans text-gray-900 antialiased dark:bg-gray-950 dark:text-gray-100">
    <div class="flex min-h-screen flex-col items-center bg-gray-100 pt-6 sm:justify-center sm:pt-0 dark:bg-gray-900">
      <div>
        {{ $slot }}
      </div>
    </div>
  </body>
</html>
