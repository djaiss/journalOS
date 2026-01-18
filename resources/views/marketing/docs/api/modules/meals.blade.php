<?php
/**
 * No view data.
 */
?>

<x-marketing-docs-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.api.index')],
  ['label' => 'Modules'],
  ['label' => 'Meals'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Meals module" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'log-meals',
        'title' => 'Log meals',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <p class="mb-2">The meals module endpoint lets you log meal details for a journal entry.</p>
        <p class="mb-2">Send any meal fields you have. At least one field is required.</p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#log-meals">
              <span class="text-orange-500">PUT</span>
              /api/journals/{id}/{year}/{month}/{day}/meals
            </a>
          </div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- PUT /api/journals/{id}/{year}/{month}/{day}/meals -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="log-meals" title="Log meals" />
        <p class="mb-10">This endpoint logs meal details for a journal entry.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the journal." />
          <x-marketing.docs.attribute required name="year" type="integer" description="The year of the journal entry." />
          <x-marketing.docs.attribute required name="month" type="integer" description="The month of the journal entry." />
          <x-marketing.docs.attribute required name="day" type="integer" description="The day of the journal entry." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute name="meal_presence" type="array" description="An array of meals you had. Possible values: breakfast, lunch, dinner, snack." />
          <x-marketing.docs.attribute name="meal_type" type="string" description="The meal type. Accepted values are: home_cooked, takeout, restaurant, work_cafeteria." />
          <x-marketing.docs.attribute name="social_context" type="string" description="Who you ate with. Accepted values are: alone, family, friends, colleagues." />
          <x-marketing.docs.attribute name="has_notes" type="string" description="Whether meal notes are enabled. Accepted values are: yes, no." />
          <x-marketing.docs.attribute name="notes" type="string" description="Optional notes about your meals." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        @include('marketing.docs.api.partials.journal-entry-response-attributes')
      </div>
      <div>
        <x-marketing.docs.code title="/api/journals/{id}/{year}/{month}/{day}/meals" verb="PUT" verbClass="text-yellow-700">
          @include('marketing.docs.api.partials.journal-entry-response')
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
