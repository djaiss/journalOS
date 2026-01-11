<?php
/**
 * No view data.
 */
?>

<!-- menu -->
<nav class="mb-10">
  <ul class="flex flex-wrap gap-4 rounded-lg border border-gray-200 bg-white p-2 dark:border-gray-700 dark:bg-gray-900">
    <li>
      <a href="{{ route('instance.index') }}" class="{{ request()->routeIs('instance.index') ? 'bg-gray-50 text-blue-600 dark:bg-gray-800' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-blue-300' }} flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium">
        <x-phosphor-users class="h-4 w-4" />
        {{ __('User management') }}
      </a>
    </li>
    <li>
      <a href="{{ route('instance.index') }}" class="{{ request()->routeIs('instance.testimonial.index') ? 'bg-gray-50 text-blue-600 dark:bg-gray-800' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-blue-300' }} flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium">
        <x-phosphor-chat-teardrop-dots class="h-4 w-4" />
        {{ __('Testimonials management') }}
      </a>
    </li>
    <li>
      <a href="{{ route('instance.index') }}" class="{{ request()->routeIs('instance.cancellation-reasons.index') ? 'bg-gray-50 text-blue-600 dark:bg-gray-800' : 'text-gray-700 hover:bg-gray-50 hover:text-blue-600 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-blue-300' }} flex items-center gap-2 rounded-md px-4 py-2 text-sm font-medium">
        <x-phosphor-prohibit-inset class="h-4 w-4" />
        {{ __('Cancellation reasons') }}
      </a>
    </li>
  </ul>
</nav>
