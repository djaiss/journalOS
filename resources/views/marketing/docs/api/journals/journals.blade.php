<x-marketing-docs-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.api.index')],
  ['label' => 'Journals'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Journals" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'get-the-journals-of-the-current-user',
        'title' => 'Get the journals of the current user',
      ],
      [
        'id' => 'get-a-journal',
        'title' => 'Get a specific journal',
      ],
      [
        'id' => 'create-a-journal',
        'title' => 'Create a journal',
      ],
      [
        'id' => 'update-a-journal',
        'title' => 'Update a journal',
      ],
      [
        'id' => 'delete-a-journal',
        'title' => 'Delete a journal',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 dark:border-gray-700 sm:grid-cols-2">
      <div>
        <p class="mb-2">This endpoint gets the journals of the current user.</p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#get-the-journals-of-the-current-user">
              <span class="text-blue-700">GET</span>
              /api/journals
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#get-a-journal">
              <span class="text-blue-700">GET</span>
              /api/journals/{id}
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#create-a-journal">
              <span class="text-green-700">POST</span>
              /api/journals
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#update-a-journal">
              <span class="text-green-700">PUT</span>
              /api/journals/{id}
            </a>
          </div>
          <div class="flex flex-col gap-y-2">
            <a href="#delete-a-journal">
              <span class="text-red-700">DELETE</span>
              /api/journals/{id}
            </a>
          </div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- GET /api/journals -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 dark:border-gray-700 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="get-the-journals-of-the-current-user" title="Get the journals of the current user" />
        <p class="mb-2">This endpoint gets the journals of the current user.</p>
        <p class="mb-10">
          This call is not
          <x-link href="{{ route('marketing.docs.index') }}#pagination">paginated</x-link>
          at the moment.
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <p class="text-gray-500">This endpoint does not have any parameters.</p>
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <p class="text-gray-500">No query parameters are available for this endpoint.</p>
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the journal." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the journal." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The name of the journal." />
          <x-marketing.docs.attribute name="attributes.slug" type="string" description="The slug of the journal." />
          <x-marketing.docs.attribute name="attributes.avatar" type="string" description="The avatar of the journal." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the journal." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/journals" verb="GET" verbClass="text-blue-700">
          @include('marketing.docs.api.partials.journal-response')
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- GET /api/journals/{id} -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 dark:border-gray-700 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="get-a-journal" title="Get a specific journal" />
        <p class="mb-10">This endpoint gets a specific journal.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the journal to get." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <p class="text-gray-500">No query parameters are available for this endpoint.</p>
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the journal." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the journal." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The name of the journal." />
          <x-marketing.docs.attribute name="attributes.slug" type="string" description="The slug of the journal." />
          <x-marketing.docs.attribute name="attributes.avatar" type="string" description="The avatar of the journal." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the journal." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/journals/{id}" verb="GET" verbClass="text-blue-700">
          @include('marketing.docs.api.partials.journal-response')
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- POST /api/journals -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 dark:border-gray-700 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="create-a-journal" title="Create a journal" />
        <p class="mb-10">This endpoint creates a new journal. It will return the journal in the response. The avatar is automatically generated.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <p class="text-gray-500">This endpoint does not have any parameters.</p>
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute required name="name" type="string" description="The name of the journal. Maximum 255 characters." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The object type. Always 'journal'." />
          <x-marketing.docs.attribute name="id" type="integer" description="The ID of the journal." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the journal." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The name of the journal." />
          <x-marketing.docs.attribute name="attributes.slug" type="string" description="The slug of the journal." />
          <x-marketing.docs.attribute name="attributes.avatar" type="string" description="The avatar of the journal." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links of the journal." />
          <x-marketing.docs.attribute name="self" type="string" description="The URL of the journal." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/journals" verb="POST" verbClass="text-green-700">
          @include('marketing.docs.api.partials.journal-response')
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- PUT /api/journals -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="update-a-journal" title="Update a journal" />
        <p class="mb-10">This endpoint updates the name of the journal. It will return the journal in the response. The avatar is automatically generated.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <p class="text-gray-500">This endpoint does not have any parameters.</p>
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute required name="name" type="string" description="The name of the journal. Maximum 255 characters." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The object type. Always 'journal'." />
          <x-marketing.docs.attribute name="id" type="integer" description="The ID of the journal." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the journal." />
          <x-marketing.docs.attribute name="attributes.name" type="string" description="The name of the journal." />
          <x-marketing.docs.attribute name="attributes.slug" type="string" description="The slug of the journal." />
          <x-marketing.docs.attribute name="attributes.avatar" type="string" description="The avatar of the journal." />
          <x-marketing.docs.attribute name="attributes.created_at" type="integer" description="The date and time the object was created, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.updated_at" type="integer" description="The date and time the object was last updated, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links of the journal." />
          <x-marketing.docs.attribute name="self" type="string" description="The URL of the journal." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/journals/{id}" verb="PUT" verbClass="text-green-700">
          @include('marketing.docs.api.partials.journal-response')
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- DELETE /api/journals/{id} -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="delete-a-journal" title="Delete a journal" />
        <p class="mb-10">This endpoint deletes a journal. It will return a success message in the response.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the journal." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <p class="text-gray-500">No query parameters are available for this endpoint.</p>
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <p class="text-gray-500">No response attributes are available for this endpoint.</p>
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/journals/{id}" verb="DELETE" verbClass="text-red-700">
          <div>No response body</div>
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
