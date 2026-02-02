<?php

/**
 * No view data.
 */
?>

<x-marketing-docs-layout :breadcrumbItems="[
  ['label' => 'Home', 'route' => route('marketing.index')],
  ['label' => 'Documentation', 'route' => route('marketing.docs.api.index')],
  ['label' => 'Modules'],
  ['label' => 'Reading'],
]">
  <div class="py-16">
    <x-marketing.docs.h1 title="Reading module" />

    <x-marketing.docs.table-of-content :items="[
      [
        'id' => 'log-reading',
        'title' => 'Log reading',
      ],
      [
        'id' => 'add-reading-book',
        'title' => 'Add a reading book',
      ],
      [
        'id' => 'remove-reading-book',
        'title' => 'Remove a reading book',
      ],
    ]" />

    <div class="mb-10 grid grid-cols-1 gap-6 border-b border-gray-200 pb-10 sm:grid-cols-2 dark:border-gray-700">
      <div>
        <p class="mb-2">The reading module endpoints let you track your daily reading and manage the books tied to a journal entry.</p>
        <p class="mb-2">Each endpoint returns the updated journal entry.</p>
      </div>
      <div>
        <x-marketing.docs.code title="Endpoints">
          <div class="flex flex-col gap-y-2">
            <a href="#log-reading">
              <span class="text-orange-500">PUT</span>
              /api/journals/{id}/{year}/{month}/{day}/reading
            </a>
            <a href="#add-reading-book">
              <span class="text-green-500">POST</span>
              /api/journals/{id}/{year}/{month}/{day}/reading/books
            </a>
            <a href="#remove-reading-book">
              <span class="text-red-500">DELETE</span>
              /api/journals/{id}/{year}/{month}/{day}/reading/books/{book}
            </a>
          </div>
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- PUT /api/journals/{id}/{year}/{month}/{day}/reading -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="log-reading" title="Log reading" />
        <p class="mb-10">This endpoint logs your reading details for a journal entry.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the journal." />
          <x-marketing.docs.attribute required name="year" type="integer" description="The year of the journal entry." />
          <x-marketing.docs.attribute required name="month" type="integer" description="The month of the journal entry." />
          <x-marketing.docs.attribute required name="day" type="integer" description="The day of the journal entry." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute name="did_read_today" type="string" description="Whether you read today. Accepted values are: yes, no." />
          <x-marketing.docs.attribute name="reading_amount" type="string" description="How much you read. Accepted values are: a few pages, one solid session, multiple sessions, deep immersion." />
          <x-marketing.docs.attribute name="mental_state" type="string" description="Your mental state after reading. Accepted values are: stimulated, calm, neutral, overloaded." />
          <x-marketing.docs.attribute name="reading_feel" type="string" description="How reading felt. Accepted values are: effortless, engaging, demanding, hard to focus." />
          <x-marketing.docs.attribute name="want_continue" type="string" description="Whether you wanted to keep reading. Accepted values are: strongly, somewhat, not really." />
          <x-marketing.docs.attribute name="reading_limit" type="string" description="What limited your reading. Accepted values are: time, energy, distraction, nothing." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        @include('marketing.docs.api.partials.journal-entry-response-attributes')
      </div>
      <div>
        <x-marketing.docs.code title="/api/journals/{id}/{year}/{month}/{day}/reading" verb="PUT" verbClass="text-yellow-700">
          @include('marketing.docs.api.partials.journal-entry-response')
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- POST /api/journals/{id}/{year}/{month}/{day}/reading/books -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="add-reading-book" title="Add a reading book" />
        <p class="mb-10">This endpoint adds a book to the reading list for a journal entry.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the journal." />
          <x-marketing.docs.attribute required name="year" type="integer" description="The year of the journal entry." />
          <x-marketing.docs.attribute required name="month" type="integer" description="The month of the journal entry." />
          <x-marketing.docs.attribute required name="day" type="integer" description="The day of the journal entry." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters>
          <x-marketing.docs.attribute required name="book_name" type="string" description="The name of the book you read. Maximum 255 characters." />
        </x-marketing.docs.query-parameters>

        <!-- response attributes -->
        @include('marketing.docs.api.partials.journal-entry-response-attributes')
      </div>
      <div>
        <x-marketing.docs.code title="/api/journals/{id}/{year}/{month}/{day}/reading/books" verb="POST" verbClass="text-green-700">
          @include('marketing.docs.api.partials.journal-entry-response')
        </x-marketing.docs.code>
      </div>
    </div>

    <!-- DELETE /api/journals/{id}/{year}/{month}/{day}/reading/books/{book} -->
    <div class="mb-10 grid grid-cols-1 gap-6 sm:grid-cols-2">
      <div>
        <x-marketing.docs.h2 id="remove-reading-book" title="Remove a reading book" />
        <p class="mb-10">This endpoint removes a book from the reading list for a journal entry.</p>

        <!-- url parameters -->
        <x-marketing.docs.url-parameters>
          <x-marketing.docs.attribute required name="id" type="integer" description="The ID of the journal." />
          <x-marketing.docs.attribute required name="year" type="integer" description="The year of the journal entry." />
          <x-marketing.docs.attribute required name="month" type="integer" description="The month of the journal entry." />
          <x-marketing.docs.attribute required name="day" type="integer" description="The day of the journal entry." />
          <x-marketing.docs.attribute required name="book" type="integer" description="The ID of the book to remove." />
        </x-marketing.docs.url-parameters>

        <!-- query parameters -->
        <x-marketing.docs.query-parameters></x-marketing.docs.query-parameters>

        <!-- response attributes -->
        @include('marketing.docs.api.partials.journal-entry-response-attributes')
      </div>
      <div>
        <x-marketing.docs.code title="/api/journals/{id}/{year}/{month}/{day}/reading/books/{book}" verb="DELETE" verbClass="text-red-700">
          @include('marketing.docs.api.partials.journal-entry-response')
        </x-marketing.docs.code>
      </div>
    </div>
  </div>
</x-marketing-docs-layout>
