<?php

/**
 * No view data.
 */
?>

{{-- @llms-title: Company --}}
{{-- @llms-description: Our handbook --}}
{{-- @llms-route: /company --}}
<x-marketing-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Company', 'route' => route('marketing.company.handbook.index')],
  ['label' => 'Handbook'],
]">
  @include('marketing.company.partials.company-header')

  <h1 class="mb-6 text-2xl font-bold dark:text-gray-100">Our handbook</h1>

  <p class="dark:text-gray-300">This handbook explains what I do, how I think and how I want to move this project forward.</p>
</x-marketing-layout>
