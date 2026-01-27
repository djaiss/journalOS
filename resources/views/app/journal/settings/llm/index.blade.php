<?php
/**
 * @var \App\Models\Journal $journal
 * @var \Illuminate\Support\ViewErrorBag $errors
 */
?>

<x-app-layout :journal="$journal">
  <x-slot:title>
    {{ __('LLM access') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => $journal->name, 'route' => route('journal.show', ['slug' => $journal->slug]) ],
    ['label' => __('Settings'), 'route' => route('journal.settings.modules.index', ['slug' => $journal->slug])],
    ['label' => __('LLM access')]
  ]" />

  <div class="grid grow bg-gray-50 sm:grid-cols-[220px_1fr] dark:bg-gray-950">
    @include('app.journal.settings.partials.sidebar', ['journal' => $journal])

    <section class="p-4 sm:p-8">
      <div class="mx-auto flex max-w-2xl flex-col gap-y-8 sm:px-0">
        <x-box padding="p-0">
          <x-slot:title>
            {{ __('LLM access') }}
          </x-slot>

          <x-slot:description>
            {{ __('You can access your account with an LLM like ChatGPT or Claude. Basically, if you activate it, we will give you a link that will let you query a specific day or a month and get in return an LLM-friendly response. With that you will be able to get stats or whatever you want with it. This URL is quite secure, but we strongly suggest that you keep it private and be very careful with it as it can access, in read mode, all your data.') }}
          </x-slot>

          <x-form method="put" x-target="llm-access-form notifications" x-target.back="llm-access-form" id="llm-access-form" :action="route('journal.settings.llm.update', ['slug' => $journal->slug])">
            <div class="grid grid-cols-3 items-center rounded-t-lg border-b border-gray-200 p-3 last:rounded-b-lg hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
              <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Allow LLM access') }}</p>
              <div class="col-span-1 w-full justify-self-end">
                <x-select id="has_llm_access" name="has_llm_access" :options="[
                  1 => __('Yes'),
                  0 => __('No'),
                ]" :selected="old('has_llm_access', $journal->has_llm_access ? 1 : 0)" required :error="$errors->get('has_llm_access')" />
              </div>
            </div>

            <!-- actions -->
            <div class="flex items-center justify-end p-3">
              <x-button>{{ __('Save') }}</x-button>
            </div>
          </x-form>
        </x-box>
      </div>
    </section>
  </div>
</x-app-layout>
