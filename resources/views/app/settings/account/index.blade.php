<x-app-layout>
  <x-slot:title>
    {{ __('Account administration') }}
  </x-slot:title>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => __('Account administration')]
  ]" />

  <div class="grid flex-grow sm:grid-cols-[220px_1fr]">
    <!-- sidebar -->
    @include('app.settings.partials.sidebar')

    <!-- main content -->
    <section class="p-4 sm:p-8">
      <div class="mx-auto flex max-w-4xl flex-col gap-y-8 sm:px-0">
        @include('app.settings.account.partials.prune-account')

        @include('app.settings.account.partials.delete-account')
      </div>
    </section>
  </div>
</x-app-layout>
