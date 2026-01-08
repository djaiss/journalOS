<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class DeleteRelatedJournalData implements ShouldQueue
{
    use Queueable;

    private const int BATCH_SIZE = 1000;

    /**
     * Add every table that has a journal_entry_id FK here.
     */
    private const array ENTRY_RELATED_TABLES = [
        'book_journal_entry' => 'journal_entry_id',
        'module_energy' => 'journal_entry_id',
        'module_kids' => 'journal_entry_id',
        'module_sleep' => 'journal_entry_id',
        'module_sexual_activity' => 'journal_entry_id',
        'module_work' => 'journal_entry_id',
        'module_travel' => 'journal_entry_id',
        'module_health' => 'journal_entry_id',
        'module_mood' => 'journal_entry_id',
        'module_day_type' => 'journal_entry_id',
        'module_physical_activity' => 'journal_entry_id',
    ];

    public function __construct(
        public int $journalId,
    ) {}

    public function handle(): void
    {
        $this->deleteJournalEntriesAndModules();
        $this->deleteLogs();
    }

    private function deleteJournalEntriesAndModules(): void
    {
        DB::table('journal_entries')
            ->where('journal_id', $this->journalId)
            ->select('id')
            ->orderedChunkById(
                self::BATCH_SIZE,
                function (Collection $rows): void {
                    $entryIds = $rows->pluck('id')->all();

                    DB::transaction(function () use ($entryIds): void {
                        foreach (self::ENTRY_RELATED_TABLES as $table => $fkColumn) {
                            DB::table($table)
                                ->whereIn($fkColumn, $entryIds)
                                ->delete();
                        }

                        DB::table('journal_entries')
                            ->whereIn('id', $entryIds)
                            ->delete();
                    });
                },
                column: 'id',
            );
    }

    private function deleteLogs(): void
    {
        DB::table('logs')
            ->where('journal_id', $this->journalId)
            ->select('id')
            ->orderedChunkById(
                self::BATCH_SIZE,
                function (Collection $rows): void {
                    $logIds = $rows->pluck('id')->all();

                    DB::transaction(function () use ($logIds): void {
                        DB::table('logs')
                            ->whereIn('id', $logIds)
                            ->delete();
                    });
                },
                column: 'id',
            );
    }
}
