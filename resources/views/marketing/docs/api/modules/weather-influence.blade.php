<?php

/**
 * No view data.
 */
?>

<x-marketing-docs-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.api.index')],
  ['label' => 'Modules'],
  ['label' => 'Weather influence'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Weather influence module" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'log-weather-influence',
        'title' => 'Log weather influence',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <p class="mb-2">The weather influence module endpoint lets you log how the weather affected you for a journal entry.</p>
        <p class="mb-2">The endpoint returns the updated journal entry.</p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#log-weather-influence">
              <span class="text-orange-500">PUT</span>
              /api/journals/{id}/{year}/{month}/{day}/weather-influence
            </a>
          </div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- PUT /api/journals/{id}/{year}/{month}/{day}/weather-influence -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="log-weather-influence" title="Log weather influence" />
        <p class="mb-10">This endpoint logs how the weather influenced your day for a journal entry.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the journal." />
          <x-marketing.docs.attribute required name="year" type="integer" description="The year of the journal entry." />
          <x-marketing.docs.attribute required name="month" type="integer" description="The month of the journal entry." />
          <x-marketing.docs.attribute required name="day" type="integer" description="The day of the journal entry." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute required name="mood_effect" type="string" description="How the weather affected your mood. Accepted values are: positive, slight, none, negative." />
          <x-marketing.docs.attribute required name="energy_effect" type="string" description="How the weather affected your energy. Accepted values are: boosted, neutral, drained." />
          <x-marketing.docs.attribute required name="plans_influence" type="string" description="How the weather influenced your plans. Accepted values are: none, slight, significant." />
          <x-marketing.docs.attribute required name="outside_time" type="string" description="How much time you spent outside. Accepted values are: a_lot, some, barely, not_at_all." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        @include('marketing.docs.api.partials.journal-entry-response-attributes')
      </div>
      <div>
        <x-marketing.docs.code title="/api/journals/{id}/{year}/{month}/{day}/weather-influence" verb="PUT" verbClass="text-yellow-700">
          @include('marketing.docs.api.partials.journal-entry-response')
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
