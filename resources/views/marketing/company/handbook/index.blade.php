<?php
/**
 * No view data.
 */
?>

{{-- @llms-title: Handbook --}}
{{-- @llms-description: Our handbook --}}
{{-- @llms-route: /company/handbook --}}
<x-marketing-handbook-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Company', 'route' => route('marketing.company.handbook.index')],
  ['label' => 'Handbook'],
]">
  <h1 class="mb-6 text-2xl font-bold dark:text-gray-100">Our handbook</h1>

  <p class="mb-6 dark:text-gray-300">This handbook explains what I do, how I think and how I want to move this project forward. Brace yourself, it's very good. At least I think so.</p>

  <div class="flex flex-col gap-y-4">
    <div class="flex items-center justify-between">
      <p class="font-semibold dark:text-gray-100">General information</p>
      <div class="ml-4 flex-grow border-b border-dashed border-gray-800 dark:border-gray-600"></div>
    </div>
    <div class="flex items-center justify-between pl-6">
      <a href="{{ route('marketing.company.handbook.project') }}" class="text-blue-500 hover:underline dark:text-blue-400">Who I am and what is this project</a>
      <div class="mx-4 flex-grow border-b border-dashed border-gray-800 dark:border-gray-600"></div>
      <p class="text-gray-600 dark:text-gray-400">{{ \App\Helpers\MarketingHelper::countWords('marketing.company.handbook.project') }} words</p>
    </div>
    <div class="flex items-center justify-between pl-6">
      <a href="{{ route('marketing.company.handbook.principles') }}" class="text-blue-500 hover:underline dark:text-blue-400">Principles</a>
      <div class="mx-4 flex-grow border-b border-dashed border-gray-800 dark:border-gray-600"></div>
      <p class="text-gray-600 dark:text-gray-400">{{ \App\Helpers\MarketingHelper::countWords('marketing.company.handbook.principles') }} words</p>
    </div>
    <div class="flex items-center justify-between pl-6">
      <a href="{{ route('marketing.company.handbook.shipping') }}" class="text-blue-500 hover:underline dark:text-blue-400">Shipping is better than not shipping</a>
      <div class="mx-4 flex-grow border-b border-dashed border-gray-800 dark:border-gray-600"></div>
      <p class="text-gray-600 dark:text-gray-400">{{ \App\Helpers\MarketingHelper::countWords('marketing.company.handbook.shipping') }} words</p>
    </div>
    <div class="flex items-center justify-between pl-6">
      <a href="{{ route('marketing.company.handbook.money') }}" class="text-blue-500 hover:underline dark:text-blue-400">How does this project make money</a>
      <div class="mx-4 flex-grow border-b border-dashed border-gray-800 dark:border-gray-600"></div>
      <p class="text-gray-600 dark:text-gray-400">{{ \App\Helpers\MarketingHelper::countWords('marketing.company.handbook.money') }} words</p>
    </div>
    <div class="flex items-center justify-between pl-6">
      <a href="{{ route('marketing.company.handbook.why-open-source') }}" class="text-blue-500 hover:underline dark:text-blue-400">Why open source</a>
      <div class="mx-4 flex-grow border-b border-dashed border-gray-800 dark:border-gray-600"></div>
      <p class="text-gray-600 dark:text-gray-400">{{ \App\Helpers\MarketingHelper::countWords('marketing.company.handbook.why-open-source') }} words</p>
    </div>
    <div class="flex items-center justify-between pl-6">
      <a href="{{ route('marketing.company.handbook.where') }}" class="text-blue-500 hover:underline dark:text-blue-400">Where am I going with this</a>
      <div class="mx-4 flex-grow border-b border-dashed border-gray-800 dark:border-gray-600"></div>
      <p class="text-gray-600 dark:text-gray-400">{{ \App\Helpers\MarketingHelper::countWords('marketing.company.handbook.where') }} words</p>
    </div>
    <div class="flex items-center justify-between">
      <p class="font-semibold dark:text-gray-100">Marketing</p>
      <div class="ml-4 flex-grow border-b border-dashed border-gray-800 dark:border-gray-600"></div>
    </div>
    <div class="flex items-center justify-between pl-6">
      <a href="{{ route('marketing.company.handbook.marketing.envision') }}" class="text-blue-500 hover:underline dark:text-blue-400">How do I envision marketing</a>
      <div class="mx-4 flex-grow border-b border-dashed border-gray-800 dark:border-gray-600"></div>
      <p class="text-gray-600 dark:text-gray-400">{{ \App\Helpers\MarketingHelper::countWords('marketing.company.handbook.marketing') }} words</p>
    </div>
    <div class="flex items-center justify-between pl-6">
      <a href="{{ route('marketing.company.handbook.marketing.social-media') }}" class="text-blue-500 hover:underline dark:text-blue-400">Social media</a>
      <div class="mx-4 flex-grow border-b border-dashed border-gray-800 dark:border-gray-600"></div>
      <p class="text-gray-600 dark:text-gray-400">{{ \App\Helpers\MarketingHelper::countWords('marketing.company.handbook.social-media') }} words</p>
    </div>
    <div class="flex items-center justify-between pl-6">
      <a href="{{ route('marketing.company.handbook.marketing.writing') }}" class="text-blue-500 hover:underline dark:text-blue-400">Writing for JournalOS</a>
      <div class="mx-4 flex-grow border-b border-dashed border-gray-800 dark:border-gray-600"></div>
      <p class="text-gray-600 dark:text-gray-400">{{ \App\Helpers\MarketingHelper::countWords('marketing.company.handbook.writing') }} words</p>
    </div>
    <div class="flex items-center justify-between pl-6">
      <a href="{{ route('marketing.company.handbook.marketing.product-philosophy') }}" class="text-blue-500 hover:underline dark:text-blue-400">Product philosophy</a>
      <div class="mx-4 flex-grow border-b border-dashed border-gray-800 dark:border-gray-600"></div>
      <p class="text-gray-600 dark:text-gray-400">{{ \App\Helpers\MarketingHelper::countWords('marketing.company.handbook.product-philosophy') }} words</p>
    </div>
    <div class="mb-10 flex items-center justify-between pl-6">
      <a href="{{ route('marketing.company.handbook.marketing.prioritize') }}" class="text-blue-500 hover:underline dark:text-blue-400">How do we prioritize features?</a>
      <div class="mx-4 flex-grow border-b border-dashed border-gray-800 dark:border-gray-600"></div>
      <p class="text-gray-600 dark:text-gray-400">{{ \App\Helpers\MarketingHelper::countWords('marketing.company.handbook.prioritize') }} words</p>
    </div>
  </div>
</x-marketing-handbook-layout>
