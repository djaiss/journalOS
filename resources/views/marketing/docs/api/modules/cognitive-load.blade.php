<?php
/**
 * No view data.
 */
?>

<x-marketing-docs-layout :breadcrumbItems="[[
  'label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.api.index')],
  ['label' => 'Modules'],
  ['label' => 'Cognitive load'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Cognitive load module" />

    <x-marketing.docs.table-of-content :items="[[
      [
        'id' => 'log-cognitive-load',
        'title' => 'Log cognitive load',
      ],
    ]]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <p class="mb-2">The cognitive load module endpoint lets you log your overall cognitive load on a specific day.</p>
        <p class="mb-2">The endpoint returns the updated journal entry.</p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#log-cognitive-load">
              <span class="text-orange-500">PUT</span>
              /api/journals/{id}/{year}/{month}/{day}/cognitive-load
            </a>
          </div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- PUT /api/journals/{id}/{year}/{month}/{day}/cognitive-load -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="log-cognitive-load" title="Log cognitive load" />
        <p class="mb-10">This endpoint logs your cognitive load for a journal entry.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the journal." />
          <x-marketing.docs.attribute required name="year" type="integer" description="The year of the journal entry." />
          <x-marketing.docs.attribute required name="month" type="integer" description="The month of the journal entry." />
          <x-marketing.docs.attribute required name="day" type="integer" description="The day of the journal entry." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute required name="cognitive_load" type="string" description="Your cognitive load on that day. Accepted values are: very low, low, high, overwhelming." />
          <x-marketing.docs.attribute name="primary_source" type="string" description="Optional. The primary source of your load. Accepted values are: work, personal life, relationships, health, uncertainty, mixed." />
          <x-marketing.docs.attribute name="load_quality" type="string" description="Optional. The quality of the load. Accepted values are: productive, mixed, mostly wasteful." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        @include('marketing.docs.api.partials.journal-entry-response-attributes')
      </div>
      <div>
        <x-marketing.docs.code title="/api/journals/{id}/{year}/{month}/{day}/cognitive-load" verb="PUT" verbClass="text-yellow-700">
          @include('marketing.docs.api.partials.journal-entry-response')
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
