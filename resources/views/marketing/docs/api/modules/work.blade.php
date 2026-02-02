<?php

/**
 * No view data.
 */
?>

<x-marketing-docs-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.api.index')],
  ['label' => 'Modules'],
  ['label' => 'Work'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Work module" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'log-work',
        'title' => 'Log work',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <p class="mb-2">The work module endpoint lets you log work details for a journal entry.</p>
        <p class="mb-2">Send any work fields you have. At least one field is required.</p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#log-work">
              <span class="text-orange-500">PUT</span>
              /api/journals/{id}/{year}/{month}/{day}/work
            </a>
          </div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- PUT /api/journals/{id}/{year}/{month}/{day}/work -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="log-work" title="Log work" />
        <p class="mb-10">This endpoint logs work details for a journal entry.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the journal." />
          <x-marketing.docs.attribute required name="year" type="integer" description="The year of the journal entry." />
          <x-marketing.docs.attribute required name="month" type="integer" description="The month of the journal entry." />
          <x-marketing.docs.attribute required name="day" type="integer" description="The day of the journal entry." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute name="worked" type="string" description="Whether you worked on this day. Accepted values are: yes, no." />
          <x-marketing.docs.attribute name="work_mode" type="string" description="The work mode for this day. Accepted values are: on-site, remote, hybrid." />
          <x-marketing.docs.attribute name="work_load" type="string" description="The work load intensity for this day. Accepted values are: light, medium, heavy." />
          <x-marketing.docs.attribute name="work_procrastinated" type="string" description="Whether you procrastinated. Accepted values are: yes, no." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        @include('marketing.docs.api.partials.journal-entry-response-attributes')
      </div>
      <div>
        <x-marketing.docs.code title="/api/journals/{id}/{year}/{month}/{day}/work" verb="PUT" verbClass="text-yellow-700">
          @include('marketing.docs.api.partials.journal-entry-response')
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
