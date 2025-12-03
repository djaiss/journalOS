<?php
/*
 * @var $emails
 */
?>

<x-app-layout>
  <x-slot:title>
    {{ __('Emails sent') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('organization.index')],
    ['label' => __('Settings'), 'route' => route('settings.profile.index')],
    ['label' => __('Emails sent')]
  ]" />

  <!-- settings layout -->
  <div class="grid flex-grow sm:grid-cols-[220px_1fr]">
    <!-- Sidebar -->
    @include('settings.partials.sidebar')

    <!-- Main content -->
    <section class="p-4 sm:p-8">
      <div class="mx-auto max-w-4xl sm:px-0">
        <x-box id="emails-sent-container" x-merge="append" padding="p-0">
          <x-slot:title>{{ __('Emails sent') }}</x-slot>
          <!-- last actions -->
          @foreach ($emails as $email)
            <div x-data="{ open: false, isLast: {{ $loop->last ? 'true' : 'false' }} }">
              <div @click="open = !open" class="group flex cursor-pointer items-center justify-between border-b border-gray-200 p-3 text-sm first:rounded-t-lg hover:bg-blue-50" :class="{'border-b-0 rounded-b-lg': !open && isLast}">
                <div class="flex items-center gap-x-3">
                  @if ($email->sent_at && ! $email->delivered_at)
                    <span class="top-0 right-0 h-4 w-4 animate-pulse rounded-full border-2 border-white bg-yellow-500"></span>
                  @elseif ($email->delivered_at && $email->sent_at)
                    <span class="top-0 right-0 h-4 w-4 animate-pulse rounded-full border-2 border-white bg-green-500"></span>
                  @elseif ($email->bounced_at)
                    <span class="top-0 right-0 h-4 w-4 animate-pulse rounded-full border-2 border-white bg-red-500"></span>
                  @endif

                  <div class="flex flex-col gap-1">
                    <div>
                      <span class="font-light text-gray-500">{{ __('To:') }}</span>
                      {{ $email->email_address }}
                    </div>
                    <div>
                      <span class="font-light text-gray-500">{{ __('Subject:') }}</span>
                      {{ $email->subject }}
                    </div>
                  </div>
                </div>

                <div class="flex items-center gap-x-3">
                  <!-- sent at && delivered at -->
                  <div class="flex flex-col gap-1">
                    <div>
                      <span class="font-light text-gray-500">{{ __('Sent at:') }}</span>
                      {{ $email->sent_at }}
                    </div>

                    @if ($email->delivered_at)
                      <div>
                        <span class="font-light text-gray-500">{{ __('Delivered at:') }}</span>
                        {{ $email->delivered_at }}
                      </div>
                    @endif
                  </div>

                  <!-- arrow -->
                  <x-phosphor-caret-down x-show="!open" class="h-4 w-4 text-gray-500 transition-transform duration-200" />
                  <x-phosphor-caret-up x-show="open" class="h-4 w-4 text-gray-500 transition-transform duration-200" />
                </div>
              </div>

              <div x-cloak x-show="open" x-transition:enter="transition duration-200 ease-out" x-transition:enter-start="-translate-y-2 transform opacity-0" x-transition:enter-end="translate-y-0 transform opacity-100" x-transition:leave="transition duration-200 ease-in" x-transition:leave-start="translate-y-0 transform opacity-100" x-transition:leave-end="-translate-y-2 transform opacity-0" class="border-b border-gray-200 bg-gray-50" :class="{'rounded-b-lg border-b-0': isLast}">
                <p class="p-2 text-center text-gray-600 italic">{{ __('We automatically remove links in this email since they are probably invalid at this time') }}</p>
                <div class="p-4">
                  {!! $email->body !!}
                </div>
              </div>
            </div>
          @endforeach

          @if ($emails->nextPageUrl())
            <div id="pagination" class="flex justify-center rounded-b-lg p-3 text-sm hover:bg-blue-50">
              <x-link x-target="emails-sent-container pagination" href="{{ $emails->nextPageUrl() }}" class="text-center">{{ __('Load more') }}</x-link>
            </div>
          @endif
        </x-box>
      </div>
    </section>
  </div>
</x-app-layout>
