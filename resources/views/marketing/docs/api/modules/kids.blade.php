<?php

/**
 * No view data.
 */
?>

<x-marketing-docs-layout :breadcrumbItems="[[
  'label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.api.index')],
  ['label' => 'Modules'],
  ['label' => 'Kids'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Kids module" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'log-kids-today',
        'title' => 'Log kids today',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <p class="mb-2">The kids module endpoint lets you log whether you had the kids today in a shared custody context.</p>
        <p class="mb-2">The endpoint returns the updated journal entry.</p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#log-kids-today">
              <span class="text-orange-500">PUT</span>
              /api/journals/{id}/{year}/{month}/{day}/kids
            </a>
          </div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- PUT /api/journals/{id}/{year}/{month}/{day}/kids -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="log-kids-today" title="Log kids today" />
        <p class="mb-10">This endpoint logs whether you had the kids today for a journal entry.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the journal." />
          <x-marketing.docs.attribute required name="year" type="integer" description="The year of the journal entry." />
          <x-marketing.docs.attribute required name="month" type="integer" description="The month of the journal entry." />
          <x-marketing.docs.attribute required name="day" type="integer" description="The day of the journal entry." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute required name="had_kids_today" type="string" description="Whether you had the kids today. Possible values: 'yes', 'no'." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        @include('marketing.docs.api.partials.journal-entry-response-attributes')
      </div>
      <div>
        <x-marketing.docs.code title="/api/journals/{id}/{year}/{month}/{day}/kids" verb="PUT" verbClass="text-yellow-700">
          @include('marketing.docs.api.partials.journal-entry-response')
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
