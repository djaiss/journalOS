<?php

declare(strict_types = 1);

namespace App\View\Presenters;

use App\Helpers\ModuleCatalog;
use App\Models\Layout;

final readonly class LayoutModulesPresenter
{
    public function __construct(
        private Layout $layout,
    ) {}

    /**
     * @return array{
     *   columns: array<int, array<int, array{key: string, label: string, position: int}>>,
     *   available_modules: array<string, string>
     * }
     */
    public function build(): array
    {
        $modules = $this->layout
            ->layoutModules()
            ->orderBy('column_number')
            ->orderBy('position')
            ->get();

        $columns = [];

        for ($column = 1; $column <= $this->layout->columns_count; $column++) {
            $columns[$column] = [];
        }

        foreach ($modules as $module) {
            $columns[$module->column_number][] = [
                'key' => $module->module_key,
                'label' => ModuleCatalog::labelFor($module->module_key),
                'position' => $module->position,
            ];
        }

        $existingKeys = $modules->pluck('module_key')->all();
        $availableModules = [];

        foreach (ModuleCatalog::moduleLabels() as $key => $label) {
            if (in_array($key, $existingKeys, true)) {
                continue;
            }

            $availableModules[$key] = $label;
        }

        return [
            'columns' => $columns,
            'available_modules' => $availableModules,
        ];
    }
}
