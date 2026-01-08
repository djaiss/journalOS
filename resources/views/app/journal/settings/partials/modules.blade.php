<div class="flex flex-col gap-4" x-data="{ activeTab: 'all' }">
  <h2 class="font-semi-bold mb-1 text-lg">{{ __('Modules') }}</h2>
  <div class="mb-2 flex flex-col gap-y-2 text-sm text-gray-500 dark:text-gray-400">
    {{ __('Manage the modules available for this journal. Disabling a module will not delete its data. It will only hide the module from the journal.') }}
  </div>

  <!-- Tabs Navigation -->
  <div>
    <div class="inline-flex h-9 items-center justify-start gap-1 rounded-lg bg-gray-100 p-1 text-gray-500 dark:bg-gray-800 dark:text-gray-400">
      <button type="button" @click="activeTab = 'all'" :class="activeTab === 'all' ? 'bg-white text-gray-900 shadow dark:bg-gray-950 dark:text-gray-50' : ''" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-md px-3 py-1 text-sm font-medium whitespace-nowrap ring-offset-white transition-all hover:bg-white hover:shadow focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 dark:ring-offset-gray-950 hover:dark:bg-gray-950 hover:dark:text-gray-50 dark:focus-visible:ring-gray-300">
        <span>{{ __('All') }}</span>
        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs dark:bg-gray-700">12</span>
      </button>
      <button type="button" @click="activeTab = 'body'" :class="activeTab === 'body' ? 'bg-white text-gray-900 shadow dark:bg-gray-950 dark:text-gray-50' : ''" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-md px-3 py-1 text-sm font-medium whitespace-nowrap ring-offset-white transition-all hover:bg-white hover:shadow focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 dark:ring-offset-gray-950 hover:dark:bg-gray-950 hover:dark:text-gray-50 dark:focus-visible:ring-gray-300">
        <span>💪</span>
        <span>{{ __('Body & Health') }}</span>
        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs dark:bg-gray-700">3</span>
      </button>
      <button type="button" @click="activeTab = 'mind'" :class="activeTab === 'mind' ? 'bg-white text-gray-900 shadow dark:bg-gray-950 dark:text-gray-50' : ''" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-md px-3 py-1 text-sm font-medium whitespace-nowrap ring-offset-white transition-all hover:bg-white hover:shadow focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 dark:ring-offset-gray-950 hover:dark:bg-gray-950 hover:dark:text-gray-50 dark:focus-visible:ring-gray-300">
        <span>🧠</span>
        <span>{{ __('Mind & Emotions') }}</span>
        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs dark:bg-gray-700">2</span>
      </button>
      <button type="button" @click="activeTab = 'work'" :class="activeTab === 'work' ? 'bg-white text-gray-900 shadow dark:bg-gray-950 dark:text-gray-50' : ''" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-md px-3 py-1 text-sm font-medium whitespace-nowrap ring-offset-white transition-all hover:bg-white hover:shadow focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 dark:ring-offset-gray-950 hover:dark:bg-gray-950 hover:dark:text-gray-50 dark:focus-visible:ring-gray-300">
        <span>💼</span>
        <span>{{ __('Work') }}</span>
        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs dark:bg-gray-700">3</span>
      </button>
      <button type="button" @click="activeTab = 'social'" :class="activeTab === 'social' ? 'bg-white text-gray-900 shadow dark:bg-gray-950 dark:text-gray-50' : ''" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-md px-3 py-1 text-sm font-medium whitespace-nowrap ring-offset-white transition-all hover:bg-white hover:shadow focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 dark:ring-offset-gray-950 hover:dark:bg-gray-950 hover:dark:text-gray-50 dark:focus-visible:ring-gray-300">
        <span>👥</span>
        <span>{{ __('Social') }}</span>
        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs dark:bg-gray-700">3</span>
      </button>
      <button type="button" @click="activeTab = 'places'" :class="activeTab === 'places' ? 'bg-white text-gray-900 shadow dark:bg-gray-950 dark:text-gray-50' : ''" class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-md px-3 py-1 text-sm font-medium whitespace-nowrap ring-offset-white transition-all hover:bg-white hover:shadow focus-visible:ring-2 focus-visible:ring-gray-950 focus-visible:ring-offset-2 focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50 dark:ring-offset-gray-950 hover:dark:bg-gray-950 hover:dark:text-gray-50 dark:focus-visible:ring-gray-300">
        <span>📍</span>
        <span>{{ __('Places') }}</span>
        <span class="rounded-full bg-gray-200 px-2 py-0.5 text-xs dark:bg-gray-700">1</span>
      </button>
    </div>
  </div>

  <!-- Tab Content: All -->
  <div x-show="activeTab === 'all'">
    <x-box padding="p-0" id="modules-container">
      <x-slot:title></x-slot>
      <x-slot:description></x-slot>

      <!-- day type module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="day-type-module-form">
        <input type="hidden" name="module" value="day_type" />
        <div class="grid grid-cols-3 items-center rounded-t-lg border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Day type module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_day_type_module">{{ $journal->show_day_type_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>

      <!-- primary obligation module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="primary-obligation-module-form">
        <input type="hidden" name="module" value="primary_obligation" />
        <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Primary obligation module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_primary_obligation_module">{{ $journal->show_primary_obligation_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>

      <!-- health module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="health-module-form">
        <input type="hidden" name="module" value="health" />
        <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Health module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_health_module">{{ $journal->show_health_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>

      <!-- mood module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="mood-module-form">
        <input type="hidden" name="module" value="mood" />
        <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Mood module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_mood_module">{{ $journal->show_mood_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>

      <!-- energy module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="energy-module-form">
        <input type="hidden" name="module" value="energy" />
        <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Energy module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_energy_module">{{ $journal->show_energy_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>

      <!-- social density module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="social-density-module-form">
        <input type="hidden" name="module" value="social_density" />
        <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Social density module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_social_density_module">{{ $journal->show_social_density_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>

      <!-- physical activity module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="physical-activity-module-form">
        <input type="hidden" name="module" value="physical_activity" />
        <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Physical activity module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_physical_activity_module">{{ $journal->show_physical_activity_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>

      <!-- sexual activity module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="sexual-activity-module-form">
        <input type="hidden" name="module" value="sexual_activity" />
        <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sexual activity module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_sexual_activity_module">{{ $journal->show_sexual_activity_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>

      <!-- sleep module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="sleep-module-form">
        <input type="hidden" name="module" value="sleep" />
        <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sleep module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_sleep_module">{{ $journal->show_sleep_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>

      <!-- travel module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="travel-module-form">
        <input type="hidden" name="module" value="travel" />
        <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Travel module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_travel_module">{{ $journal->show_travel_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>

      <!-- kids module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="kids-module-form">
        <input type="hidden" name="module" value="kids" />
        <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Kids module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_kids_module">{{ $journal->show_kids_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>

      <!-- work module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container" id="work-module-form">
        <input type="hidden" name="module" value="work" />
        <div class="grid grid-cols-3 items-center rounded-b-lg p-3 hover:bg-blue-50 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Work module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_work_module">{{ $journal->show_work_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>
    </x-box>
  </div>

  <!-- Tab Content: Body & Health -->
  <div x-show="activeTab === 'body'" x-cloak>
    <x-box padding="p-0">
      <x-slot:title></x-slot>
      <x-slot:description></x-slot>

      <!-- sleep module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container">
        <input type="hidden" name="module" value="sleep" />
        <div class="grid grid-cols-3 items-center rounded-t-lg border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sleep module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_sleep_module">{{ $journal->show_sleep_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>

      <!-- health module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container">
        <input type="hidden" name="module" value="health" />
        <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Health module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_health_module">{{ $journal->show_health_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>

      <!-- physical activity module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container">
        <input type="hidden" name="module" value="physical_activity" />
        <div class="grid grid-cols-3 items-center rounded-b-lg p-3 hover:bg-blue-50 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Physical activity module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_physical_activity_module">{{ $journal->show_physical_activity_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>
    </x-box>
  </div>

  <!-- Tab Content: Mind & Emotions -->
  <div x-show="activeTab === 'mind'" x-cloak>
    <x-box padding="p-0">
      <x-slot:title></x-slot>
      <x-slot:description></x-slot>

      <!-- mood module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container">
        <input type="hidden" name="module" value="mood" />
        <div class="grid grid-cols-3 items-center rounded-t-lg border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Mood module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_mood_module">{{ $journal->show_mood_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>

      <!-- energy module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container">
        <input type="hidden" name="module" value="energy" />
        <div class="grid grid-cols-3 items-center rounded-b-lg p-3 hover:bg-blue-50 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Energy module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_energy_module">{{ $journal->show_energy_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>
    </x-box>
  </div>

  <!-- Tab Content: Work & Responsibilities -->
  <div x-show="activeTab === 'work'" x-cloak>
    <x-box padding="p-0">
      <x-slot:title></x-slot>
      <x-slot:description></x-slot>

      <!-- work module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container">
        <input type="hidden" name="module" value="work" />
        <div class="grid grid-cols-3 items-center rounded-t-lg border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Work module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_work_module">{{ $journal->show_work_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>

      <!-- primary obligation module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container">
        <input type="hidden" name="module" value="primary_obligation" />
        <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Primary obligation module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_primary_obligation_module">{{ $journal->show_primary_obligation_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>

      <!-- day type module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container">
        <input type="hidden" name="module" value="day_type" />
        <div class="grid grid-cols-3 items-center rounded-b-lg p-3 hover:bg-blue-50 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Day type module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_day_type_module">{{ $journal->show_day_type_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>
    </x-box>
  </div>

  <!-- Tab Content: Relationships & Social Life -->
  <div x-show="activeTab === 'social'" x-cloak>
    <x-box padding="p-0">
      <x-slot:title></x-slot>
      <x-slot:description></x-slot>

      <!-- social density module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container">
        <input type="hidden" name="module" value="social_density" />
        <div class="grid grid-cols-3 items-center rounded-t-lg border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Social density module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_social_density_module">{{ $journal->show_social_density_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>

      <!-- sexual activity module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container">
        <input type="hidden" name="module" value="sexual_activity" />
        <div class="grid grid-cols-3 items-center border-b border-gray-200 p-3 hover:bg-blue-50 dark:border-gray-700 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Sexual activity module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_sexual_activity_module">{{ $journal->show_sexual_activity_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>

      <!-- kids module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container">
        <input type="hidden" name="module" value="kids" />
        <div class="grid grid-cols-3 items-center rounded-b-lg p-3 hover:bg-blue-50 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Kids module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_kids_module">{{ $journal->show_kids_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>
    </x-box>
  </div>

  <!-- Tab Content: Movement & Places -->
  <div x-show="activeTab === 'places'" x-cloak>
    <x-box padding="p-0">
      <x-slot:title></x-slot>
      <x-slot:description></x-slot>

      <!-- travel module -->
      <x-form method="put" action="{{ route('journal.settings.modules.update', ['slug' => $journal->slug]) }}" x-target="modules-container notifications" x-target.back="modules-container">
        <input type="hidden" name="module" value="travel" />
        <div class="grid grid-cols-3 items-center rounded-lg p-3 hover:bg-blue-50 dark:hover:bg-gray-800">
          <p class="col-span-2 block text-sm font-medium text-gray-700 dark:text-gray-300">{{ __('Travel module') }}</p>
          <div class="w-full justify-self-start">
            <x-toggle name="enabled" :checked="$journal->show_travel_module">{{ $journal->show_travel_module ? __('Enabled') : __('Disabled') }}</x-toggle>
          </div>
        </div>
      </x-form>
    </x-box>
  </div>
</div>
