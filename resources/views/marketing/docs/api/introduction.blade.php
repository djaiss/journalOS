<?php
/**
 * No view data.
 */
?>

<x-marketing-docs-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.api.index')],
  ['label' => 'Introduction'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="API reference" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'test-the-api-yourself',
        'title' => 'Test the API yourself',
      ],
      [
        'id' => 'conventions-of-the-api',
        'title' => 'Conventions of the API',
      ],
      [
        'id' => 'pagination',
        'title' => 'Pagination',
      ],
      [
        'id' => 'health',
        'title' => 'Health',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <p class="mb-2">The {{ config('app.name') }} API is organized around REST. Our API has predictable resource-oriented URLs.</p>
        <p class="mb-2">
          You
          <strong>can not</strong>
          use the {{ config('app.name') }} API in test mode. This means all requests will be processed towards your production account. Please be cautious.
        </p>
        <p>The {{ config('app.name') }} API doesn’t support bulk updates. You can work on only one object per request.</p>
      </div>

      <div>
        <h2 class="mb-2 text-lg font-bold">Base URL</h2>
        <x-marketing.docs.code>{{ config('app.url') }}/api</x-marketing.docs.code>
      </div>
    </div>

    <div class="mb-10 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <x-marketing.docs.h2 id="test-the-api-yourself" title="Test the API yourself" />
      <p class="mb-2">
        If you want to test the API yourself, we provide two convenient tools for you to use:
        <x-link href="https://www.usebruno.com/" target="_blank">Bruno</x-link>
        .
      </p>
      <p class="mb-2">
        The documentation is included in the GitHub repository, under the
        <x-link href="https://github.com/djaiss/peopleOS/tree/main/docs" target="_blank">docs</x-link>
        folder.
      </p>
      <p>Why these tools? Because they're fresh, new, free and open source under the MIT license, and I really like their ethos.</p>
    </div>

    <div class="mb-10 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <x-marketing.docs.h2 id="conventions-of-the-api" title="Conventions of the API" />
      <p class="mb-2">
        There is no strict standard for JSON payloads, but we do try to follow
        <x-link href="https://jsonapi.org/" target="_blank">the JSON:API specification</x-link>
        , which defines a structured format for responses.
      </p>
    </div>

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <x-marketing.docs.h2 id="pagination" title="Pagination" />
        <p class="mb-2">All endpoints that return a collection of resources support pagination.</p>
        <p class="mb-2">
          The default value for
          <code>per_page</code>
          is 10. This can not be changed at the moment.
        </p>
        <p class="mb-2">All responses will include links to navigate to the next and previous pages.</p>
      </div>
      <div>
        <x-marketing.docs.code title="Example of pagination" verb="GET" verbClass="text-green-700">
          <div>{</div>
          <div class="pl-4">
            "meta":
            <span class="text-rose-800">{</span>
          </div>
          <div class="pl-8">
            "current_page":
            <span class="text-lime-700">1</span>
            ,
          </div>
          <div class="pl-8">
            "from":
            <span class="text-lime-700">1</span>
            ,
          </div>
          <div class="pl-8">
            "last_page":
            <span class="text-lime-700">1</span>
            ,
          </div>
          <div class="pl-8">
            "links":
            <span class="text-rose-800">[</span>
          </div>
          <div class="pl-12">{</div>
          <div class="pl-16">
            "url":
            <span class="text-rose-800">null</span>
            ,
          </div>
          <div class="pl-16">
            "label":
            <span class="text-rose-800">"&laquo; Previous"</span>
            ,
          </div>
          <div class="pl-16">
            "page":
            <span class="text-rose-800">null</span>
            ,
          </div>
          <div class="pl-16">
            "active":
            <span class="text-rose-800">false</span>
          </div>
          <div class="pl-12">},</div>
          <div class="pl-12">{</div>
          <div class="pl-16">
            "url":
            <span class="text-rose-800">"{{ config('app.url') }}/api/settings/logs?page=1"</span>
            ,
          </div>
          <div class="pl-16">
            "label":
            <span class="text-rose-800">"1"</span>
            ,
          </div>
          <div class="pl-16">
            "page":
            <span class="text-lime-700">1</span>
            ,
          </div>
          <div class="pl-16">
            "active":
            <span class="text-rose-800">true</span>
          </div>
          <div class="pl-12">},</div>
          <div class="pl-12">{</div>
          <div class="pl-16">
            "url":
            <span class="text-rose-800">null</span>
            ,
          </div>
          <div class="pl-16">
            "label":
            <span class="text-rose-800">"Next &raquo;"</span>
            ,
          </div>
          <div class="pl-16">
            "page":
            <span class="text-rose-800">null</span>
            ,
          </div>
          <div class="pl-16">
            "active":
            <span class="text-rose-800">false</span>
          </div>
          <div class="pl-12">
            <span class="text-rose-800">]</span>
          </div>
          <div class="pl-8">
            <span class="text-rose-800">},</span>
          </div>
          <div class="pl-8">
            "path":
            <span class="text-rose-800">"{{ config('app.url') }}/api/settings/logs"</span>
            ,
          </div>
          <div class="pl-8">
            "per_page":
            <span class="text-lime-700">10</span>
            ,
          </div>
          <div class="pl-8">
            "to":
            <span class="text-lime-700">1</span>
            ,
          </div>
          <div class="pl-8">
            "total":
            <span class="text-lime-700">1</span>
          </div>
          <div class="pl-4">
            <span class="text-rose-800">}</span>
          </div>
          <div>}</div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- GET /api/health -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="health" title="Health" />
        <p class="mb-10">This endpoint checks the health of the application and returns a simple "ok" message. It lets you know if the application is running and if the database is connected.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <p class="text-gray-500">This endpoint does not have any parameters.</p>
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <p class="text-gray-500">This endpoint does not have any parameters.</p>
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        <x-marketing.docs.response-attributes>
          <x-marketing.docs.attribute name="message" type="string" description="The message of the response." />
          <x-marketing.docs.attribute name="status" type="integer" description="The status code of the response." />
        </x-marketing.docs.response-attributes>
      </div>
      <div>
        <x-marketing.docs.code title="/api/health" verb="GET" verbClass="text-green-700">
          <div>{</div>
          <div class="pl-4">
            "message":
            <span class="text-rose-800">"ok"</span>
            ,
          </div>
          <div class="pl-4">
            "status":
            <span class="text-lime-700">200</span>
            ,
          </div>
          <div>}</div>
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
