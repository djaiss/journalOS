<?php
/**
 * @var \App\Models\Journal $journal
 * @var \App\Models\Layout $layout
 * @var array<int, array<int, array{key: string, label: string, position: int}>> $columns
 * @var array<string, string> $availableModules
 */
?>

<x-app-layout :journal="$journal">
  <x-slot:title>
    {{ __('Layout modules') }}
  </x-slot>

  <x-breadcrumb :items="[
    ['label' => __('Dashboard'), 'route' => route('journal.index')],
    ['label' => $journal->name, 'route' => route('journal.show', ['slug' => $journal->slug]) ],
    ['label' => __('Modules'), 'route' => route('journal.settings.modules.index', ['slug' => $journal->slug])],
    ['label' => __('Layout modules')]
  ]" />

  <div class="grid grow bg-gray-50 sm:grid-cols-[220px_1fr] dark:bg-gray-950">
    @include('app.journal.settings.partials.sidebar', ['journal' => $journal])

    <section class="p-4 sm:p-8">
      <div class="flex w-full flex-col gap-y-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
          <div>
            <h2 class="text-lg font-semibold">{{ $layout->name }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __(':count columns', ['count' => $layout->columns_count]) }}</p>
          </div>
        </div>

        <div
          id="layout-modules-container"
          x-data="{
            dragging: null,
            draggingColumn: null,
            dropTarget: null,
            startDrag(moduleKey, columnNumber) {
              this.dragging = moduleKey
              this.draggingColumn = columnNumber
            },
            clearDrag() {
              this.dragging = null
              this.draggingColumn = null
              this.dropTarget = null
            },
            markDropTarget(columnNumber, position) {
              this.dropTarget = `${columnNumber}:${position}`
            },
            reorder(columnNumber, position, sameColumnPosition = null) {
              if (! this.dragging) {
                return
              }

              const targetPosition =
                sameColumnPosition !== null && this.draggingColumn === columnNumber
                  ? sameColumnPosition
                  : position

              this.$refs.reorderModuleKey.value = this.dragging
              this.$refs.reorderColumnNumber.value = columnNumber
              this.$refs.reorderPosition.value = targetPosition
              this.$refs.reorderForm.requestSubmit()
            },
          }">
          <x-form x-ref="reorderForm" x-target="layout-modules-container notifications" method="put" action="{{ route('journal.settings.layouts.modules.reorder', ['slug' => $journal->slug, 'layout' => $layout->id]) }}" class="hidden">
            <input type="hidden" name="module_key" x-ref="reorderModuleKey" />
            <input type="hidden" name="column_number" x-ref="reorderColumnNumber" />
            <input type="hidden" name="position" x-ref="reorderPosition" />
          </x-form>

          <div class="flex flex-col gap-4 sm:grid" style="grid-template-columns: repeat({{ $layout->columns_count }}, minmax(0, 1fr))">
            @foreach ($columns as $columnNumber => $modules)
              <div class="flex min-h-[16rem] flex-col rounded-lg border border-gray-200 bg-white p-4 shadow-xs dark:border-gray-800 dark:bg-gray-900">
                <div class="mb-3 flex items-center justify-between">
                  <p class="text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('Column :number', ['number' => $columnNumber]) }}</p>
                  <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-300">{{ count($modules) }}</span>
                </div>

                <div class="flex flex-1 flex-col gap-2">
                  @forelse ($modules as $module)
                    <div draggable="true" @dragstart="startDrag('{{ $module['key'] }}', {{ $columnNumber }})" @dragend="clearDrag()" @dragover.prevent="markDropTarget({{ $columnNumber }}, {{ $module['position'] }})" @drop.prevent="reorder({{ $columnNumber }}, {{ $module['position'] }})" :class="dropTarget === '{{ $columnNumber }}:{{ $module['position'] }}' ? 'border-blue-400 ring-2 ring-blue-200 dark:ring-blue-900/40' : 'border-gray-200 dark:border-gray-700'" class="group flex items-center justify-between rounded-lg border bg-white px-3 py-2 text-sm text-gray-700 shadow-xs transition dark:bg-gray-950 dark:text-gray-100">
                      <div class="flex items-center gap-2">
                        <x-phosphor-dots-six-vertical class="h-4 w-4 text-gray-400 cursor-move" />
                        <span>{{ $module['label'] }}</span>
                      </div>
                      <x-form x-target="layout-modules-container notifications" method="delete" action="{{ route('journal.settings.layouts.modules.destroy', ['slug' => $journal->slug, 'layout' => $layout->id, 'moduleKey' => $module['key']]) }}">
                        <x-button.secondary class="text-xs">
                          {{ __('Remove') }}
                        </x-button.secondary>
                      </x-form>
                    </div>
                  @empty
                    <div class="flex flex-1 items-center justify-center rounded-lg border border-dashed border-gray-300 bg-gray-50 p-3 text-xs text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400">
                      {{ __('No modules yet') }}
                    </div>
                  @endforelse

                  <div class="rounded-lg border border-dashed border-gray-200 bg-gray-50 p-2 text-center text-xs text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400" @dragover.prevent="markDropTarget({{ $columnNumber }}, {{ count($modules) + 1 }})" @drop.prevent="reorder({{ $columnNumber }}, {{ count($modules) + 1 }}, {{ max(1, count($modules)) }})" :class="dropTarget === '{{ $columnNumber }}:{{ count($modules) + 1 }}' ? 'border-blue-400 text-blue-700 dark:text-blue-200' : ''">
                    {{ __('Drop here to move') }}
                  </div>
                </div>

                <div class="mt-4 border-t border-gray-200 pt-3 dark:border-gray-800" x-data="{ open: false }">
                  <x-button.secondary type="button" class="text-sm" x-on:click="open = !open">
                    {{ __('Add module') }}
                  </x-button.secondary>

                  <!-- modal to add module -->
                  <div x-show="open" x-cloak class="mt-3">
                    @if (count($availableModules) > 0)
                      <x-form method="post" action="{{ route('journal.settings.layouts.modules.store', ['slug' => $journal->slug, 'layout' => $layout->id]) }}" x-target="layout-modules-container notifications" class="flex flex-col gap-2">
                        <input type="hidden" name="column_number" value="{{ $columnNumber }}" />
                        <div class="space-y-2">
                          <x-label for="layout-module-{{ $columnNumber }}" :value="__('Module')" />
                          <select id="layout-module-{{ $columnNumber }}" name="module_key" class="block h-10 w-full appearance-none rounded-lg border border-gray-200 border-b-gray-300/80 bg-white px-3 py-2 text-base leading-[1.375rem] text-gray-700 shadow-xs aria-invalid:border-red-500 sm:text-sm dark:border-white/10 dark:bg-white/10 dark:text-gray-300 dark:shadow-none" required>
                            @foreach ($availableModules as $moduleKey => $label)
                              <option value="{{ $moduleKey }}">{{ $label }}</option>
                            @endforeach
                          </select>
                          <x-error :messages="$errors->get('module_key')" />
                        </div>
                        <x-button.secondary type="submit" class="text-sm">
                          {{ __('Add') }}
                        </x-button.secondary>
                      </x-form>
                    @else
                      <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('All modules are already in this layout.') }}</p>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
    </section>
  </div>
</x-app-layout>
