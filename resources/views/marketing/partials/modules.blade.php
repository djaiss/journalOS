<div class="border border-gray-200 rounded-lg bg-white p-4">
<div class="mx-auto max-w-7xl px-6 lg:px-8 xl:px-0">
  <div class="flex w-full flex-col gap-y-6" x-data="{
    dragging: null,
    draggingColumn: null,
    dropTarget: null,
    columns: {
      1: [
        { key: 'sleep', name: 'Sleep module' },
        { key: 'energy', name: 'Energy module' },
        { key: 'health', name: 'Health module' },
        { key: 'physical_activity', name: 'Physical activity module' },
        { key: 'hygiene', name: 'Hygiene module' }
      ],
      2: [
        { key: 'mood', name: 'Mood module' },
        { key: 'work', name: 'Work module' },
        { key: 'day_type', name: 'Day type module' },
        { key: 'primary_obligation', name: 'Primary obligation module' },
        { key: 'shopping', name: 'Shopping module' }
      ],
      3: [
        { key: 'travel', name: 'Travel module' },
        { key: 'weather', name: 'Weather module' },
        { key: 'social_density', name: 'Social density module' }
      ]
    },
    availableModules: [
      { key: 'weather_influence', name: 'Weather influence module' },
      { key: 'meals', name: 'Meals module' },
      { key: 'kids', name: 'Kids module' },
      { key: 'reading', name: 'Reading module' },
      { key: 'sexual_activity', name: 'Sexual activity module' },
      { key: 'cognitive_load', name: 'Cognitive load module' }
    ],
    showAddForm: { 1: false, 2: false, 3: false },
    selectedModule: { 1: '', 2: '', 3: '' },
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
      if (!this.dragging) return

      const sourceColumn = this.draggingColumn
      const targetColumn = columnNumber

      // Find and remove the module from source column
      const moduleIndex = this.columns[sourceColumn].findIndex(m => m.key === this.dragging)
      if (moduleIndex === -1) return

      const [module] = this.columns[sourceColumn].splice(moduleIndex, 1)

      // Calculate target position
      let targetPosition = position - 1
      if (sourceColumn === targetColumn && sameColumnPosition !== null) {
        targetPosition = sameColumnPosition - 1
        if (moduleIndex < targetPosition) {
          targetPosition--
        }
      } else if (sourceColumn === targetColumn && moduleIndex < position - 1) {
        targetPosition--
      }

      // Insert at target position
      this.columns[targetColumn].splice(targetPosition, 0, module)

      this.clearDrag()
    },
    removeModule(moduleKey, columnNumber) {
      const index = this.columns[columnNumber].findIndex(m => m.key === moduleKey)
      if (index !== -1) {
        this.columns[columnNumber].splice(index, 1)
      }
    },
    addModule(columnNumber) {
      const moduleKey = this.selectedModule[columnNumber]
      if (!moduleKey) return

      const module = this.availableModules.find(m => m.key === moduleKey)
      if (module) {
        this.columns[columnNumber].push({ ...module })
        this.showAddForm[columnNumber] = false
        this.selectedModule[columnNumber] = ''
      }
    },
    getColumnCount(columnNumber) {
      return this.columns[columnNumber].length
    }
  }">
    <div class="flex flex-wrap items-start justify-between gap-3">
      <div>
        <h2 class="text-lg font-semibold">Daily meditation</h2>
        <p class="text-sm text-gray-500 dark:text-gray-400">Manage the modules available for this journal. Disabling a module will not delete its data. It will only hide the module from the journal.</p>
      </div>
    </div>

    <div class="flex flex-col gap-4 sm:flex-row sm:items-start">
      <template x-for="colNum in [1, 2, 3]" :key="colNum">
        <div class="flex flex-1 flex-col gap-4">
          <div class="flex min-h-[16rem] flex-col rounded-lg border border-gray-200 bg-white p-4 shadow-xs dark:border-gray-800 dark:bg-gray-900">
            <div class="mb-3 flex items-center justify-between">
              <p class="text-sm font-semibold text-gray-700 dark:text-gray-200" x-text="`Column ${colNum}`"></p>
              <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-600 dark:bg-gray-800 dark:text-gray-300" x-text="getColumnCount(colNum)"></span>
            </div>

            <div class="flex flex-col gap-2">
              <template x-for="(module, index) in columns[colNum]" :key="module.key">
                <div
                  draggable="true"
                  @dragstart="startDrag(module.key, colNum)"
                  @dragend="clearDrag()"
                  @dragover.prevent="markDropTarget(colNum, index + 1)"
                  @drop.prevent="reorder(colNum, index + 1)"
                  :class="dropTarget === `${colNum}:${index + 1}` ? 'border-blue-400 ring-2 ring-blue-200 dark:ring-blue-900/40' : 'border-gray-200 dark:border-gray-700'"
                  class="group flex items-center justify-between rounded-lg border bg-white px-3 py-2 text-sm text-gray-700 shadow-xs transition dark:bg-gray-950 dark:text-gray-100">
                  <div class="flex items-center gap-2">
                    <svg class="h-4 w-4 cursor-move text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor">
                      <path d="M104,60A12,12,0,1,1,92,48,12,12,0,0,1,104,60Zm60,12a12,12,0,1,0-12-12A12,12,0,0,0,164,72ZM92,116a12,12,0,1,0,12,12A12,12,0,0,0,92,116Zm72,0a12,12,0,1,0,12,12A12,12,0,0,0,164,116ZM92,184a12,12,0,1,0,12,12A12,12,0,0,0,92,184Zm72,0a12,12,0,1,0,12,12A12,12,0,0,0,164,184Z"></path>
                    </svg>
                    <span x-text="module.name"></span>
                  </div>
                  <button
                    type="button"
                    @click="removeModule(module.key, colNum)"
                    class="relative inline-flex h-8 transform-gpu cursor-pointer items-center justify-center gap-2 rounded-lg text-sm font-medium whitespace-nowrap transition duration-150 ease-out active:translate-y-[1px] active:scale-[0.97] active:shadow-inner active:ease-in disabled:pointer-events-none disabled:cursor-default disabled:opacity-75 aria-pressed:z-10 dark:disabled:opacity-75 [:where(&)]:px-3 border border-gray-300 border-b-gray-300/80 bg-white text-gray-800 shadow-xs hover:border-gray-400 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:border-gray-600 dark:hover:bg-gray-600/75 text-xs">
                    Remove
                  </button>
                </div>
              </template>

              <div
                class="rounded-lg border border-dashed border-gray-200 bg-gray-50 p-2 text-center text-xs text-gray-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-400"
                @dragover.prevent="markDropTarget(colNum, getColumnCount(colNum) + 1)"
                @drop.prevent="reorder(colNum, getColumnCount(colNum) + 1, getColumnCount(colNum))"
                :class="dropTarget === `${colNum}:${getColumnCount(colNum) + 1}` ? 'border-blue-400 text-blue-700 dark:text-blue-200' : ''">
                Drop here to move
              </div>
            </div>
          </div>

          <!-- Add module section -->
          <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-xs dark:border-gray-800 dark:bg-gray-900">
            <div class="text-center" x-show="!showAddForm[colNum]">
              <button
                type="button"
                @click="showAddForm[colNum] = true"
                class="relative inline-flex h-8 transform-gpu cursor-pointer items-center justify-center gap-2 rounded-lg text-sm font-medium whitespace-nowrap transition duration-150 ease-out active:translate-y-[1px] active:scale-[0.97] active:shadow-inner active:ease-in disabled:pointer-events-none disabled:cursor-default disabled:opacity-75 aria-pressed:z-10 dark:disabled:opacity-75 [:where(&)]:px-3 border border-gray-300 border-b-gray-300/80 bg-white text-gray-800 shadow-xs hover:border-gray-400 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:border-gray-600 dark:hover:bg-gray-600/75 text-sm">
                <span class="shrink-0">
                  <svg class="h-4 w-4 transition-transform duration-150 group-hover:-translate-x-1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 256 256" fill="currentColor">
                    <path d="M128,24A104,104,0,1,0,232,128,104.11,104.11,0,0,0,128,24Zm0,192a88,88,0,1,1,88-88A88.1,88.1,0,0,1,128,216Zm48-88a8,8,0,0,1-8,8H136v32a8,8,0,0,1-16,0V136H88a8,8,0,0,1,0-16h32V88a8,8,0,0,1,16,0v32h32A8,8,0,0,1,176,128Z"></path>
                  </svg>
                </span>
                Add module
              </button>
            </div>

            <div x-show="showAddForm[colNum]" style="display: none;">
              <div class="flex flex-col gap-2">
                <div class="space-y-2">
                  <label class="block text-sm leading-tight font-medium text-gray-800 dark:text-white" :for="`layout-module-${colNum}`">Module</label>
                  <select
                    :id="`layout-module-${colNum}`"
                    x-model="selectedModule[colNum]"
                    class="block h-10 w-full appearance-none rounded-lg border border-gray-200 border-b-gray-300/80 bg-white px-3 py-2 text-base leading-[1.375rem] text-gray-700 shadow-xs aria-invalid:border-red-500 sm:text-sm dark:border-white/10 dark:bg-white/10 dark:text-gray-300 dark:shadow-none"
                    required>
                    <option value="">Select a module...</option>
                    <template x-for="module in availableModules" :key="module.key">
                      <option :value="module.key" x-text="module.name"></option>
                    </template>
                  </select>
                </div>

                <div class="mt-2 flex items-center justify-between gap-2">
                  <button
                    type="button"
                    @click="showAddForm[colNum] = false; selectedModule[colNum] = ''"
                    class="relative inline-flex h-8 transform-gpu cursor-pointer items-center justify-center gap-2 rounded-lg text-sm font-medium whitespace-nowrap transition duration-150 ease-out active:translate-y-[1px] active:scale-[0.97] active:shadow-inner active:ease-in disabled:pointer-events-none disabled:cursor-default disabled:opacity-75 aria-pressed:z-10 dark:disabled:opacity-75 [:where(&)]:px-3 border border-gray-300 border-b-gray-300/80 bg-white text-gray-800 shadow-xs hover:border-gray-400 hover:bg-gray-50 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:hover:border-gray-600 dark:hover:bg-gray-600/75 text-sm">
                    Cancel
                  </button>
                  <button
                    type="button"
                    @click="addModule(colNum)"
                    class="relative inline-flex h-8 cursor-pointer items-center justify-center gap-2 rounded-lg text-sm font-medium whitespace-nowrap transition-[transform,box-shadow] duration-150 focus-visible:ring-2 focus-visible:outline-none active:scale-[0.97] disabled:pointer-events-none disabled:cursor-default disabled:opacity-75 aria-pressed:z-10 dark:disabled:opacity-75 [:where(&)]:px-3 border border-black/10 bg-[var(--color-accent)] text-[var(--color-accent-foreground)] shadow-[inset_0px_1px_--theme(--color-white/.2)] hover:bg-[color-mix(in_oklab,_var(--color-accent),_transparent_10%)] focus-visible:ring-[var(--color-accent)]/50 active:shadow-[inset_0_2px_4px_0_rgba(0,0,0,0.35),inset_0_0_0_1px_rgba(0,0,0,0.25)] dark:border-0 text-sm">
                    Add
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>
</div>
