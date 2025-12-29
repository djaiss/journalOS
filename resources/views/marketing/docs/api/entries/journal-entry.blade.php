<x-marketing-docs-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.api.index')],
  ['label' => 'Journal entries'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Journal entries" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'get-a-journal-entry',
        'title' => 'Get a specific journal entry',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2">
      <div>
        <p class="mb-2">This endpoint gets the details of a specific journal entry.</p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#get-a-journal-entry">
              <span class="text-blue-700">GET</span>
              /api/journals/{id}/{year}/{month}/{day}
            </a>
          </div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- GET /api/journals/{id}/{year}/{month}/{day} -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="get-a-journal-entry" title="Get a specific journal entry" />
        <p class="mb-10">This endpoint returns the details of a specific journal entry by date.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the journal." />
          <x-marketing.docs.attribute required name="year" type="integer" description="The year of the journal entry." />
          <x-marketing.docs.attribute required name="month" type="integer" description="The month of the journal entry." />
          <x-marketing.docs.attribute required name="day" type="integer" description="The day of the journal entry." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <p class="text-gray-500">No query parameters are available for this endpoint.</p>
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the journal entry." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the journal entry." />
          <x-marketing.docs.attribute name="attributes.journal_id" type="integer" description="The ID of the journal." />
          <x-marketing.docs.attribute name="attributes.day" type="integer" description="The day of the journal entry." />
          <x-marketing.docs.attribute name="attributes.month" type="integer" description="The month of the journal entry." />
          <x-marketing.docs.attribute name="attributes.year" type="integer" description="The year of the journal entry." />
          <x-marketing.docs.attribute name="attributes.modules" type="object" description="The modules included with the journal entry." />
          <x-marketing.docs.attribute name="attributes.modules.sleep" type="object" description="The sleep module payload." />
          <x-marketing.docs.attribute name="attributes.modules.sleep.bedtime" type="string" description="The bedtime time of the journal entry." />
          <x-marketing.docs.attribute name="attributes.modules.sleep.wake_up_time" type="string" description="The wake up time of the journal entry." />
          <x-marketing.docs.attribute name="attributes.modules.sleep.sleep_duration_in_minutes" type="integer" description="The sleep duration in minutes." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the journal entry." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/journals/{id}/{year}/{month}/{day}" verb="GET" verbClass="text-blue-700">
          @include('marketing.docs.api.partials.journal-entry-response')
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
