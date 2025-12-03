<x-app-layout>
  <x-slot:title>
    {{ __('Account administration') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('organization.index')],
    ['label' => __('Account administration')]
  ]" />

  <div class="grid flex-grow sm:grid-cols-[220px_1fr]">
    <!-- sidebar -->
    @include('settings.partials.sidebar')

    <!-- main content -->
    <section class="p-4 sm:p-8">
      <div class="mx-auto flex max-w-4xl flex-col gap-y-8 sm:px-0">
        <x-box>
          <x-slot:title>
            {{ __('Delete your account') }}
          </x-slot>

          <x-slot:description>
            {{ __('Your account and all data will be deleted immediately and cannot be restored. This is irreversible. Please be certain.') }}
          </x-slot>

          <form action="{{ route('settings.account.destroy') }}" method="post" x-data="{
            feedback: '',
            isValid: false,
            async handleSubmit() {
              if (! this.isValid) return

              if (
                await confirm(
                  '{{ __('Are you absolutely sure? This action cannot be undone.') }}',
                )
              ) {
                $el.submit()
              }
            },
          }" @submit.prevent="handleSubmit">
            @csrf
            @method('delete')

            <label for="feedback" class="mb-2 block text-sm font-medium text-red-700">
              {{ __('Please tell us why you are leaving (required)') }}
            </label>

            <div class="mt-1">
              <textarea id="feedback" name="feedback" rows="3" x-model="feedback" @input="isValid = feedback.length >= 3" class="h-auto w-full rounded-md border border-gray-300 px-3 py-2 shadow-xs placeholder:text-neutral-400 focus:border-indigo-500 focus:ring-1 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" placeholder="{{ __('Your feedback helps us improve our service...') }}"></textarea>
            </div>

            <div class="mt-4 flex items-center justify-end gap-x-3">
              <button type="submit" x-bind:disabled="!isValid" x-bind:class="! isValid ? 'opacity-50 cursor-not-allowed' : ''" class="inline-flex items-center gap-x-2 rounded-md bg-red-600 px-3.5 py-2 text-sm font-semibold text-white shadow-xs hover:bg-red-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600">
                <x-phosphor-trash class="h-4 w-4" />
                {{ __('Delete account') }}
              </button>
            </div>
          </form>
        </x-box>
      </div>
    </section>
  </div>
</x-app-layout>
