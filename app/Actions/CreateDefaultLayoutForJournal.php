<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Journal;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Support\Facades\DB;

final class CreateDefaultLayoutForJournal
{
    public function __construct(
        private readonly Journal $journal,
    ) {}

    public function execute(): void
    {
        /** @var Encrypter $encrypter */
        $encrypter = app(Encrypter::class);

        $layoutId = DB::table('layouts')->insertGetId([
            'journal_id' => $this->journal->id,
            'name' => $encrypter->encrypt('Default', false),
            'columns_count' => 3,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $modules = [
            // Column 1: Body & Health
            1 => ['health', 'hygiene', 'energy', 'physical_activity', 'sleep'],
            // Column 2: Mind & Work
            2 => ['mood', 'work', 'day_type', 'primary_obligation', 'shopping'],
            // Column 3: Movement & Social
            3 => ['travel', 'social_density'],
        ];

        $insertData = [];
        foreach ($modules as $columnNumber => $moduleKeys) {
            $position = 1;
            foreach ($moduleKeys as $moduleKey) {
                $insertData[] = [
                    'layout_id' => $layoutId,
                    'module_key' => $moduleKey,
                    'column_number' => $columnNumber,
                    'position' => $position,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $position++;
            }
        }

        DB::table('layout_modules')->insert($insertData);
    }
}
