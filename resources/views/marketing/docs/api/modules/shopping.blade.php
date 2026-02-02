<?php

/**
 * No view data.
 */
?>

<x-marketing-docs-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.api.index')],
  ['label' => 'Modules'],
  ['label' => 'Shopping'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Shopping module" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'log-shopping',
        'title' => 'Log shopping',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <p class="mb-2">The shopping module endpoint lets you log shopping details for a journal entry.</p>
        <p class="mb-2">Send any shopping fields you have. At least one field is required.</p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#log-shopping">
              <span class="text-orange-500">PUT</span>
              /api/journals/{id}/{year}/{month}/{day}/shopping
            </a>
          </div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- PUT /api/journals/{id}/{year}/{month}/{day}/shopping -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="log-shopping" title="Log shopping" />
        <p class="mb-10">This endpoint logs shopping details for a journal entry.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the journal." />
          <x-marketing.docs.attribute required name="year" type="integer" description="The year of the journal entry." />
          <x-marketing.docs.attribute required name="month" type="integer" description="The month of the journal entry." />
          <x-marketing.docs.attribute required name="day" type="integer" description="The day of the journal entry." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute name="has_shopped" type="string" description="Whether you shopped today. Accepted values are: yes, no." />
          <x-marketing.docs.attribute name="shopping_types" type="array" description="An array of shopping types. Possible values: groceries, clothes, electronics_tech, household_essentials, books_media, gifts, online_shopping, other." />
          <x-marketing.docs.attribute name="shopping_intent" type="string" description="The intent behind shopping. Accepted values are: planned, opportunistic, impulse, replacement." />
          <x-marketing.docs.attribute name="shopping_context" type="string" description="Who you shopped with. Accepted values are: alone, with_partner, with_kids." />
          <x-marketing.docs.attribute name="shopping_for" type="string" description="Who the shopping was for. Accepted values are: for_self, for_household, for_others." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        @include('marketing.docs.api.partials.journal-entry-response-attributes')
      </div>
      <div>
        <x-marketing.docs.code title="/api/journals/{id}/{year}/{month}/{day}/shopping" verb="PUT" verbClass="text-yellow-700">
          @include('marketing.docs.api.partials.journal-entry-response')
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
