<x-marketing-docs-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.api.index')],
  ['label' => 'Profile'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Profile" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'get-the-information-about-the-logged-user',
        'title' => 'Get the information about the logged user',
      ],
      [
        'id' => 'update-the-information-about-the-logged-user',
        'title' => 'Update the information about the logged user',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2">
      <div>
        <p class="mb-2">The profile endpoint is used to get and set the current user's profile information.</p>
        <p class="mb-2">This will probably not be used often, but it can be useful.</p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#get-the-information-about-the-logged-user">
              <span class="text-blue-700">GET</span>
              /api/me
            </a>
            <a href="#update-the-information-about-the-logged-user">
              <span class="text-orange-500">PUT</span>
              /api/me
            </a>
          </div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- GET /api/me -->
    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="get-the-information-about-the-logged-user" title="Get the information about the logged user" />
        <p class="mb-10">This endpoint gets the information about the logged user. This endpoint is there to make sure that the API works.</p>

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
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the user." />
          <x-marketing.docs.attribute name="first_name" type="string" description="The first name of the user." />
          <x-marketing.docs.attribute name="last_name" type="string" description="The last name of the user." />
          <x-marketing.docs.attribute name="nickname" type="string" description="The nickname of the user." />
          <x-marketing.docs.attribute name="email" type="string" description="The email of the user." />
          <x-marketing.docs.attribute name="locale" type="string" description="The locale of the user." />
          <x-marketing.docs.attribute name="created_at" type="integer" description="The creation date of the user, in Unix timestamp format." />
          <x-marketing.docs.attribute name="updated_at" type="integer" description="The last update date of the user, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The link to access the user." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/me" verb="GET" verbClass="text-blue-700">
          @include('docs.api.partials.profile-response')
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- PUT /api/me -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="update-the-information-about-the-logged-user" title="Update the information about the logged user" />
        <p class="mb-2">This endpoint updates the information about the logged user.</p>
        <p class="mb-2">Only the logged user can update these fields.</p>
        <p class="mb-2">If the email is changed, the system will send a new verification email to verify the new email address, and unless the user verifies the new email address, he/she will not be able to access the account.</p>
        <p class="mb-10">Please note that your password can not be changed through the API at the moment.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <p class="text-gray-500">This endpoint does not have any parameters.</p>
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute name="first_name" required="true" type="string" description="The first name of the user. Max 255 characters." />
          <x-marketing.docs.attribute name="last_name" required="true" type="string" description="The last name of the user. Max 255 characters." />
          <x-marketing.docs.attribute name="nickname" type="string" description="The nickname of the user. Max 255 characters." />
          <x-marketing.docs.attribute name="email" required="true" type="string" description="The email of the user. This email should be unique in the instance, and we will validate the email format. Max 255 characters." />
          <x-marketing.docs.attribute name="locale" type="string" description="The locale of the user. Format: en, fr, etc. Max 255 characters." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="type" type="string" description="The type of the resource." />
          <x-marketing.docs.attribute name="id" type="string" description="The ID of the user." />
          <x-marketing.docs.attribute name="first_name" type="string" description="The first name of the user." />
          <x-marketing.docs.attribute name="last_name" type="string" description="The last name of the user." />
          <x-marketing.docs.attribute name="nickname" type="string" description="The nickname of the user." />
          <x-marketing.docs.attribute name="email" type="string" description="The email of the user." />
          <x-marketing.docs.attribute name="locale" type="string" description="The locale of the user." />
          <x-marketing.docs.attribute name="created_at" type="integer" description="The creation date of the user, in Unix timestamp format." />
          <x-marketing.docs.attribute name="updated_at" type="integer" description="The last update date of the user, in Unix timestamp format." />
          <x-marketing.docs.attribute name="links" type="object" description="The link to access the user." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/me" verb="PUT" verbClass="text-yellow-700">
          @include('docs.api.partials.profile-response')
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
