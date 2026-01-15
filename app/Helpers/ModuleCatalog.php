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

    /**
     * @return array<string, string>
     */
    public static function moduleLabels(): array
    {
        return [
            'sleep' => __('Sleep module'),
            'work' => __('Work module'),
            'travel' => __('Travel module'),
            'shopping' => __('Shopping module'),
            'kids' => __('Kids module'),
            'day_type' => __('Day type module'),
            'primary_obligation' => __('Primary obligation module'),
            'physical_activity' => __('Physical activity module'),
            'health' => __('Health module'),
            'hygiene' => __('Hygiene module'),
            'mood' => __('Mood module'),
            'sexual_activity' => __('Sexual activity module'),
            'energy' => __('Energy module'),
            'social_density' => __('Social density module'),
        ];
    }

    public static function labelFor(string $moduleKey): string
    {
        $labels = self::moduleLabels();

        return $labels[$moduleKey] ?? $moduleKey;
    }
}
