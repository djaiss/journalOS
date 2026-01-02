<x-marketing-docs-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.api.index')],
  ['label' => 'Modules'],
  ['label' => 'Physical activity'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Physical activity module" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'log-physical-activity',
        'title' => 'Log physical activity',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <p class="mb-2">The physical activity module endpoint lets you log physical activity details for a journal entry.</p>
        <p class="mb-2">The endpoint returns the updated journal entry.</p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#log-physical-activity">
              <span class="text-orange-500">PUT</span>
              /api/journals/{id}/{year}/{month}/{day}/physical-activity
            </a>
          </div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- PUT /api/journals/{id}/{year}/{month}/{day}/physical-activity -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="log-physical-activity" title="Log physical activity" />
        <p class="mb-10">This endpoint logs physical activity details for a journal entry. You can log whether you did physical activity, the type of activity, and its intensity.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the journal." />
          <x-marketing.docs.attribute required name="year" type="integer" description="The year of the journal entry." />
          <x-marketing.docs.attribute required name="month" type="integer" description="The month of the journal entry." />
          <x-marketing.docs.attribute required name="day" type="integer" description="The day of the journal entry." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute required name="has_done_physical_activity" type="string" description="Whether you did physical activity. Accepted values are: yes, no." />
          <x-marketing.docs.attribute name="activity_type" type="string" description="The type of activity performed. Accepted values are: running, cycling, swimming, gym, walking." />
          <x-marketing.docs.attribute name="activity_intensity" type="string" description="The intensity of the activity. Accepted values are: light, moderate, intense." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        @include('marketing.docs.api.partials.journal-entry-response-attributes')
      </div>
      <div>
        <x-marketing.docs.code title="/api/journals/{id}/{year}/{month}/{day}/physical-activity" verb="PUT" verbClass="text-yellow-700">
          @include('marketing.docs.api.partials.journal-entry-response')
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
