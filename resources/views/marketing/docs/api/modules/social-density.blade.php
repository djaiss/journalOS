<?php

/**
 * No view data.
 */
?>

<x-marketing-docs-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.api.index')],
  ['label' => 'Modules'],
  ['label' => 'Social density'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Social density module" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'log-social-density',
        'title' => 'Log social density',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <p class="mb-2">The social density module endpoint lets you log how crowded things felt on a specific day.</p>
        <p class="mb-2">The endpoint returns the updated journal entry.</p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#log-social-density">
              <span class="text-orange-500">PUT</span>
              /api/journals/{id}/{year}/{month}/{day}/social-density
            </a>
          </div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- PUT /api/journals/{id}/{year}/{month}/{day}/social-density -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="log-social-density" title="Log social density" />
        <p class="mb-10">This endpoint logs your social density for a journal entry.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the journal." />
          <x-marketing.docs.attribute required name="year" type="integer" description="The year of the journal entry." />
          <x-marketing.docs.attribute required name="month" type="integer" description="The month of the journal entry." />
          <x-marketing.docs.attribute required name="day" type="integer" description="The day of the journal entry." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute required name="social_density" type="string" description="How crowded the day felt. Accepted values are: alone, few people, crowd, too much." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        @include('marketing.docs.api.partials.journal-entry-response-attributes')
      </div>
      <div>
        <x-marketing.docs.code title="/api/journals/{id}/{year}/{month}/{day}/social-density" verb="PUT" verbClass="text-yellow-700">
          @include('marketing.docs.api.partials.journal-entry-response')
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
