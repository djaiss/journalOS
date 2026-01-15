<?php

declare(strict_types=1);

namespace App\Helpers;

final class ModuleCatalog
{
    /**
     * @var array<string, array{table: string}>
     */
    private const array MODULES = [
        'sleep' => ['table' => 'module_sleep'],
        'work' => ['table' => 'module_work'],
        'travel' => ['table' => 'module_travel'],
        'shopping' => ['table' => 'module_shopping'],
        'kids' => ['table' => 'module_kids'],
        'day_type' => ['table' => 'module_day_type'],
        'primary_obligation' => ['table' => 'module_primary_obligation'],
        'physical_activity' => ['table' => 'module_physical_activity'],
        'health' => ['table' => 'module_health'],
        'hygiene' => ['table' => 'module_hygiene'],
        'mood' => ['table' => 'module_mood'],
        'sexual_activity' => ['table' => 'module_sexual_activity'],
        'energy' => ['table' => 'module_energy'],
        'social_density' => ['table' => 'module_social_density'],
    ];

    /**
     * @return array<int, string>
     */
    public static function moduleKeys(): array
    {
        return array_keys(self::MODULES);
    }

    /**
     * @return array<string, string>
     */
    public static function entryModuleTables(): array
    {
        $tables = [];

        foreach (self::MODULES as $module) {
            $tables[$module['table']] = 'journal_entry_id';
        }

        return $tables;
    }
}
