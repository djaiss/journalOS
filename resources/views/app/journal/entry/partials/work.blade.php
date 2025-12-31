<x-module>
  <x-slot:title>{{ __('Work') }}</x-slot>
  <x-slot:emoji>💼</x-slot>
  <x-slot:action>
    <div id="work-reset">
      @if ($module['display_reset'])
        <x-form x-target="work-container notifications work-reset" :action="$module['reset_url']" method="put">
          <button type="submit" class="inline cursor-pointer underline decoration-gray-300 underline-offset-4 transition-colors duration-200 hover:text-blue-600 hover:decoration-blue-400 hover:decoration-[1.15px] dark:decoration-gray-600 dark:hover:text-blue-400 dark:hover:decoration-blue-400">
            {{ __('Reset') }}
          </button>
        </x-form>
      @endif
    </div>
  </x-slot>

  <div id="work-container" class="space-y-4">
    <div class="space-y-4">
      <!-- Have you worked today? -->
      <div class="space-y-2">
        <p>{{ __('Have you worked today?') }}</p>
        <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
          <x-button.yes name="worked" value="yes" x-target="work-container notifications work-reset" :action="$module['has_worked_url']" selected="{{ $entry->worked === 'yes' }}" />
          <x-button.no name="worked" value="no" x-target="work-container notifications work-reset" :action="$module['has_worked_url']" selected="{{ $entry->worked === 'no' }}" />
        </div>
      </div>

      <!-- Work mode -->
      <div class="space-y-2">
        <p>{{ __('How did you work?') }}</p>
        <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
          <div class="flex-1 border-r border-gray-200 first:overflow-hidden first:rounded-l-lg last:rounded-r-lg last:border-r-0 dark:border-gray-700">
            <x-form x-target="work-container notifications work-reset" :action="$module['work_mode_url']" method="put" class="h-full">
              <input type="hidden" name="work_mode" value="remote" />
              <button type="submit" class="{{ $entry->work_mode === 'remote' ? 'bg-green-200 dark:bg-green-900/60' : '' }} flex h-full w-full cursor-pointer items-center justify-center p-2 text-center hover:bg-green-50 dark:hover:bg-green-900/40">
                {{ __('Remote') }}
              </button>
            </x-form>
          </div>
          <div class="flex-1 border-r border-gray-200 first:overflow-hidden first:rounded-l-lg last:rounded-r-lg last:border-r-0 dark:border-gray-700">
            <x-form x-target="work-container notifications work-reset" :action="$module['work_mode_url']" method="put" class="h-full">
              <input type="hidden" name="work_mode" value="on-site" />
              <button type="submit" class="{{ $entry->work_mode === 'on-site' ? 'bg-green-200 dark:bg-green-900/60' : '' }} flex h-full w-full cursor-pointer items-center justify-center p-2 text-center hover:bg-green-50 dark:hover:bg-green-900/40">
                {{ __('On-site') }}
              </button>
            </x-form>
          </div>
          <div class="flex-1 border-r border-gray-200 first:overflow-hidden first:rounded-l-lg last:rounded-r-lg last:border-r-0 dark:border-gray-700">
            <x-form x-target="work-container notifications work-reset" :action="$module['work_mode_url']" method="put" class="h-full">
              <input type="hidden" name="work_mode" value="hybrid" />
              <button type="submit" class="{{ $entry->work_mode === 'hybrid' ? 'bg-green-200 dark:bg-green-900/60' : '' }} flex h-full w-full cursor-pointer items-center justify-center p-2 text-center hover:bg-green-50 dark:hover:bg-green-900/40">
                {{ __('Hybrid') }}
              </button>
            </x-form>
          </div>
        </div>
      </div>

      <!-- Work load -->
      <div class="space-y-2">
        <p>{{ __('How much did you work?') }}</p>
        <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
          <div class="flex-1 border-r border-gray-200 first:overflow-hidden first:rounded-l-lg last:rounded-r-lg last:border-r-0 dark:border-gray-700">
            <x-form x-target="sleep-container notifications reset" :action="'bla'" method="put" class="h-full">
              <input type="hidden" name="bedtime" value="" />
              <button type="submit" class=" flex h-full w-full cursor-pointer items-center justify-center p-2 text-center hover:bg-green-50 dark:hover:bg-green-900/40">
                {{ __('Light') }}
              </button>
            </x-form>
          </div>
          <div class="flex-1 border-r border-gray-200 first:overflow-hidden first:rounded-l-lg last:rounded-r-lg last:border-r-0 dark:border-gray-700">
            <x-form x-target="sleep-container notifications reset" :action="'bla'" method="put" class="h-full">
              <input type="hidden" name="bedtime" value="" />
              <button type="submit" class=" flex h-full w-full cursor-pointer items-center justify-center p-2 text-center hover:bg-green-50 dark:hover:bg-green-900/40">
                {{ __('Normal') }}
              </button>
            </x-form>
          </div>
          <div class="flex-1 border-r border-gray-200 first:overflow-hidden first:rounded-l-lg last:rounded-r-lg last:border-r-0 dark:border-gray-700">
            <x-form x-target="sleep-container notifications reset" :action="'bla'" method="put" class="h-full">
              <input type="hidden" name="bedtime" value="" />
              <button type="submit" class=" flex h-full w-full cursor-pointer items-center justify-center p-2 text-center hover:bg-green-50 dark:hover:bg-green-900/40">
                {{ __('Heavy') }}
              </button>
            </x-form>
          </div>
        </div>
      </div>

      <!-- Procrastination -->
      <div class="space-y-2">
        <p>{{ __('Did you procrastinate (be honest)?') }}</p>
        <div class="flex w-full rounded-lg border border-gray-200 dark:border-gray-700">
          <x-button.yes name="worked" value="yes" x-target="work-container notifications work-reset" :action="$module['has_worked_url']" selected="{{ $entry->worked === 'yes' }}" />
          <x-button.no name="worked" value="no" x-target="work-container notifications work-reset" :action="$module['has_worked_url']" selected="{{ $entry->worked === 'no' }}" />
        </div>
      </div>
    </div>
  </div>
</x-module>
