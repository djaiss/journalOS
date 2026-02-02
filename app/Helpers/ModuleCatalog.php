<?php

declare(strict_types = 1);

namespace App\Helpers;

final class ModuleCatalog
{
    /**
     * @var array<string, array{table: string, label: string, title: string, emoji: string}>
     */
    private const array MODULES = [
        'sleep' => [
            'table' => 'module_sleep',
            'label' => 'Sleep module',
            'title' => 'Sleep tracking',
            'emoji' => '🌖',
        ],
        'work' => [
            'table' => 'module_work',
            'label' => 'Work module',
            'title' => 'Work',
            'emoji' => '💼',
        ],
        'travel' => [
            'table' => 'module_travel',
            'label' => 'Travel module',
            'title' => 'Travel',
            'emoji' => '✈️',
        ],
        'weather' => [
            'table' => 'module_weather',
            'label' => 'Weather module',
            'title' => 'Weather',
            'emoji' => '🌦️',
        ],
        'weather_influence' => [
            'table' => 'module_weather_influence',
            'label' => 'Weather influence module',
            'title' => 'Weather influence',
            'emoji' => '🌬️',
        ],
        'shopping' => [
            'table' => 'module_shopping',
            'label' => 'Shopping module',
            'title' => 'Shopping',
            'emoji' => '🛍️',
        ],
        'meals' => [
            'table' => 'module_meals',
            'label' => 'Meals module',
            'title' => 'Meals',
            'emoji' => '🍽️',
        ],
        'kids' => [
            'table' => 'module_kids',
            'label' => 'Kids module',
            'title' => 'Kids today',
            'emoji' => '🧒',
        ],
        'day_type' => [
            'table' => 'module_day_type',
            'label' => 'Day type module',
            'title' => 'Day type',
            'emoji' => '📅',
        ],
        'primary_obligation' => [
            'table' => 'module_primary_obligation',
            'label' => 'Primary obligation module',
            'title' => 'Primary obligation',
            'emoji' => '🎯',
        ],
        'physical_activity' => [
            'table' => 'module_physical_activity',
            'label' => 'Physical activity module',
            'title' => 'Physical Activity',
            'emoji' => '🏃‍♂️',
        ],
        'health' => [
            'table' => 'module_health',
            'label' => 'Health module',
            'title' => 'Health',
            'emoji' => '❤️',
        ],
        'hygiene' => [
            'table' => 'module_hygiene',
            'label' => 'Hygiene module',
            'title' => 'Hygiene',
            'emoji' => '🧼',
        ],
        'mood' => [
            'table' => 'module_mood',
            'label' => 'Mood module',
            'title' => 'Mood',
            'emoji' => '🙂',
        ],
        'reading' => [
            'table' => 'module_reading',
            'label' => 'Reading module',
            'title' => 'Reading',
            'emoji' => '📚',
        ],
        'sexual_activity' => [
            'table' => 'module_sexual_activity',
            'label' => 'Sexual activity module',
            'title' => 'Sexual activity',
            'emoji' => '❤️',
        ],
        'energy' => [
            'table' => 'module_energy',
            'label' => 'Energy module',
            'title' => 'Energy',
            'emoji' => '⚡️',
        ],
        'cognitive_load' => [
            'table' => 'module_cognitive_load',
            'label' => 'Cognitive load module',
            'title' => 'Cognitive load',
            'emoji' => '🧠',
        ],
        'social_density' => [
            'table' => 'module_social_density',
            'label' => 'Social density module',
            'title' => 'Social density',
            'emoji' => '👥',
        ],
        'social_events' => [
            'table' => 'module_social_events',
            'label' => 'Social events module',
            'title' => 'Social events',
            'emoji' => '🤝',
        ],
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
        $labels = [];

        foreach (self::MODULES as $key => $module) {
            $labels[$key] = __($module['label']);
        }

        return $labels;
    }

    public static function labelFor(string $moduleKey): string
    {
        $labels = self::moduleLabels();

        return $labels[$moduleKey] ?? $moduleKey;
    }

    /**
     * @return array<string, string>
     */
    public static function moduleEmojis(): array
    {
        $emojis = [];

        foreach (self::MODULES as $key => $module) {
            $emojis[$key] = $module['emoji'];
        }

        return $emojis;
    }

    public static function emojiFor(string $moduleKey): string
    {
        $emojis = self::moduleEmojis();

        return $emojis[$moduleKey] ?? '';
    }

    /**
     * @return array<string, string>
     */
    public static function moduleTitles(): array
    {
        $titles = [];

        foreach (self::MODULES as $key => $module) {
            $titles[$key] = __($module['title']);
        }

        return $titles;
    }

    public static function titleFor(string $moduleKey): string
    {
        $titles = self::moduleTitles();

        return $titles[$moduleKey] ?? $moduleKey;
    }
}
