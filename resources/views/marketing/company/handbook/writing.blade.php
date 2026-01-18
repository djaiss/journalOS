<?php
/**
 * No view data.
 */
?>

{{-- @llms-title: Writing --}}
{{-- @llms-description: Writing for JournalOS --}}
{{-- @llms-route: /company/handbook/writing --}}
<x-marketing-handbook-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Company', 'route' => route('marketing.company.handbook.index')],
  ['label' => 'Handbook', 'route' => route('marketing.company.handbook.index')],
  ['label' => 'Writing for JournalOS'],
]">
  <h1 class="mb-6 text-2xl font-bold">Writing for JournalOS</h1>

  <div class="prose">
    <p class="mb-2">There are two ways of writing for JournalOS:</p>

    <ul>
      <li>the user interface in the product,</li>
      <li>the marketing material.</li>
    </ul>

    <p class="mb-2">For the user interface, the tone should be simple, direct, informative, and serious. Actions in the user interface should use the imperative tone. For instance, we would say “Save” instead of “I save.”</p>

    <p class="mb-2">For the marketing material, we should be really approachable, funny, give a lot of value, and don't take ourselves seriously at all.</p>

    <p class="mb-10">In all cases though, we should remain extremely humble. We know nothing, really, and we merely exist for the sole purpose of helping others.</p>
  </div>

  <x-slot name="rightSidebar">
    <x-marketing.handbook-stats :stats="$stats" />
  </x-slot>
</x-marketing-handbook-layout>
