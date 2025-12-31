<x-marketing-docs-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.api.index')],
  ['label' => 'Emails'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Emails" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'get-the-emails-of-the-current-user',
        'title' => 'Get the emails of the current user',
      ],
      [
        'id' => 'get-an-email',
        'title' => 'Get a specific email',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <p class="mb-2">This endpoint gets the emails that were sent to the current user.</p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#get-the-emails-of-the-current-user">
              <span class="text-blue-700">GET</span>
              /api/settings/emails
            </a>
          </div>
          <a href="#get-an-email">
            <span class="text-blue-700">GET</span>
            /api/settings/emails/{id}
          </a>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- GET /api/settings/emails -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <x-marketing.docs.h2 id="get-the-emails-of-the-current-user" title="Get the emails of the current user" />
        <p class="mb-2">This endpoint gets the emails that were sent to the current user.</p>
        <p class="mb-10">
          This call is
          <x-link href="{{ route('marketing.docs.index') }}#pagination" :hover="true" :navigate="false">paginated</x-link>
          , and the default page size is 10. This can not be changed.
        </p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <p class="text-gray-500">This endpoint does not have any parameters.</p>
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute name="page" type="integer" description="The page number to retrieve. The first page is 1. If you don't provide this parameter, the first page will be returned." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the email." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the email." />
          <x-marketing.docs.attribute name="attributes.uuid" type="string" description="The UUID of the email." />
          <x-marketing.docs.attribute name="attributes.email_type" type="string" description="The type of email that was sent." />
          <x-marketing.docs.attribute name="attributes.email_address" type="string" description="The email address that received the message." />
          <x-marketing.docs.attribute name="attributes.subject" type="string" description="The subject of the email." />
          <x-marketing.docs.attribute name="attributes.body" type="string" description="The body of the email." />
          <x-marketing.docs.attribute name="attributes.sent_at" type="integer" description="The date and time the email was sent, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.delivered_at" type="integer" description="The date and time the email was delivered, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.bounced_at" type="integer" description="The date and time the email was bounced, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the email." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/settings/emails" verb="GET" verbClass="text-blue-700">
          @include('marketing.docs.api.partials.email-response')
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- GET /api/settings/emails/{email} -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="get-an-email" title="Get a specific email" />
        <p class="mb-10">This endpoint gets a specific email.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="email" type="integer" description="The ID of the email to get." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <p class="text-gray-500">No query parameters are available for this endpoint.</p>
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the email." />
          <x-marketing.docs.attribute name="attributes" type="object" description="The attributes of the email." />
          <x-marketing.docs.attribute name="attributes.uuid" type="string" description="The UUID of the email." />
          <x-marketing.docs.attribute name="attributes.email_type" type="string" description="The type of email that was sent." />
          <x-marketing.docs.attribute name="attributes.email_address" type="string" description="The email address that received the message." />
          <x-marketing.docs.attribute name="attributes.subject" type="string" description="The subject of the email." />
          <x-marketing.docs.attribute name="attributes.body" type="string" description="The body of the email." />
          <x-marketing.docs.attribute name="attributes.sent_at" type="integer" description="The date and time the email was sent, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.delivered_at" type="integer" description="The date and time the email was delivered, in Unix timestamp format." />
          <x-marketing.docs.attribute name="attributes.bounced_at" type="integer" description="The date and time the email was bounced, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The links to access the email." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/settings/emails/{email}" verb="GET" verbClass="text-blue-700">
          @include('marketing.docs.api.partials.email-response')
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
