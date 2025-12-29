<x-marketing-docs-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.api.index')],
  ['label' => 'Manage account'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Manage account" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'prune-the-account',
        'title' => 'Prune the account',
      ],
      [
        'id' => 'delete-the-account',
        'title' => 'Delete the account',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2">
      <div>
        <p class="mb-2">These endpoints help users manage account lifecycle tasks.</p>
        <p class="mb-2">You can prune journals while keeping login access, or permanently delete the account.</p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#prune-the-account">
              <span class="text-orange-500">PUT</span>
              /api/settings/prune
            </a>
            <a href="#delete-the-account">
              <span class="text-red-700">DELETE</span>
              /api/settings/account
            </a>
          </div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- PUT /api/settings/prune -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="prune-the-account" title="Prune the account" />
        <p class="mb-10">This endpoint removes all journals for the authenticated user.</p>

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
          <x-marketing.docs.attribute name="message" type="string" description="Confirmation message for the prune request." />
          <x-marketing.docs.attribute name="status" type="integer" description="HTTP status code." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/settings/prune" verb="PUT" verbClass="text-yellow-700">
          <div>{</div>
          <div class="pl-4">
            <span class="text-lime-700">"message"</span>: <span class="text-amber-700">"The account has been pruned"</span>,
          </div>
          <div class="pl-4">
            <span class="text-lime-700">"status"</span>: <span class="text-indigo-700">200</span>
          </div>
          <div>}</div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- DELETE /api/settings/account -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="delete-the-account" title="Delete the account" />
        <p class="mb-10">This endpoint permanently deletes the authenticated user account.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <p class="text-gray-500">This endpoint does not have any parameters.</p>
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute required name="reason" type="string" description="Reason for deleting the account. Minimum 3 characters." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="message" type="string" description="Confirmation message for the delete request." />
          <x-marketing.docs.attribute name="status" type="integer" description="HTTP status code." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/settings/account" verb="DELETE" verbClass="text-red-700">
          <div>{</div>
          <div class="pl-4">
            <span class="text-lime-700">"message"</span>: <span class="text-amber-700">"The account has been deleted"</span>,
          </div>
          <div class="pl-4">
            <span class="text-lime-700">"status"</span>: <span class="text-indigo-700">200</span>
          </div>
          <div>}</div>
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
