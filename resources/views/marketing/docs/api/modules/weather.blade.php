<?php

/**
 * No view data.
 */
?>

<x-marketing-docs-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.api.index')],
  ['label' => 'Modules'],
  ['label' => 'Weather'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Weather module" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'log-weather',
        'title' => 'Log weather',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <p class="mb-2">The weather module endpoint lets you log the weather for a journal entry.</p>
        <p class="mb-2">The endpoint returns the updated journal entry.</p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#log-weather">
              <span class="text-orange-500">PUT</span>
              /api/journals/{id}/{year}/{month}/{day}/weather
            </a>
          </div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- PUT /api/journals/{id}/{year}/{month}/{day}/weather -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="log-weather" title="Log weather" />
        <p class="mb-10">This endpoint logs weather details for a journal entry.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the journal." />
          <x-marketing.docs.attribute required name="year" type="integer" description="The year of the journal entry." />
          <x-marketing.docs.attribute required name="month" type="integer" description="The month of the journal entry." />
          <x-marketing.docs.attribute required name="day" type="integer" description="The day of the journal entry." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute required name="condition" type="string" description="The weather condition. Accepted values are: sunny, cloudy, rain, snow, mixed." />
          <x-marketing.docs.attribute required name="temperature_range" type="string" description="The temperature range. Accepted values are: very_cold, cold, mild, warm, hot." />
          <x-marketing.docs.attribute required name="precipitation" type="string" description="The precipitation level. Accepted values are: none, light, heavy." />
          <x-marketing.docs.attribute required name="daylight" type="string" description="The daylight length. Accepted values are: very_short, normal, very_long." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        @include('marketing.docs.api.partials.journal-entry-response-attributes')
      </div>
      <div>
        <x-marketing.docs.code title="/api/journals/{id}/{year}/{month}/{day}/weather" verb="PUT" verbClass="text-yellow-700">
          @include('marketing.docs.api.partials.journal-entry-response')
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
