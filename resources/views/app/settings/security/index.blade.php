<x-app-layout>
  <x-slot:title>
    {{ __('Security and access') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => __('Security and access')]
  ]" />

  <!-- settings layout -->
  <div class="grid flex-grow sm:grid-cols-[220px_1fr]">
    <!-- Sidebar -->
    @include('app.settings.partials.sidebar')

    <!-- Main content -->
    <section class="p-4 sm:p-8">
      <div class="mx-auto max-w-2xl space-y-6 sm:px-0">
        <!-- user password -->
        @include('app.settings.security.partials.password', ['user' => $user])

        <!-- two factor authentication -->
        @include('app.settings.security.partials.2fa.index')

        <!-- auto delete account -->
        @include('app.settings.security.partials.auto-delete')

        <!-- api keys -->
        @include('app.settings.security.partials.api.index', ['apiKeys' => $apiKeys])
      </div>
    </section>
  </div>
</x-app-layout>
