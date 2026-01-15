<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Helpers\ModuleCatalog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class DeleteRelatedJournalData implements ShouldQueue
{
    use Queueable;

    private const int BATCH_SIZE = 1000;

    /**
     * Add every non-module table that has a journal_entry_id FK here.
     */
    private const array ENTRY_RELATED_TABLES = [
        'book_journal_entry' => 'journal_entry_id',
    ];

    public function __construct(
        public int $journalId,
    ) {}

    public function handle(): void
    {
        $this->deleteLayouts();
        $this->deleteJournalEntriesAndModules();
        $this->deleteLogs();
    }

    private function deleteLayouts(): void
    {
        DB::table('layouts')
            ->where('journal_id', $this->journalId)
            ->select('id')
            ->orderedChunkById(
                self::BATCH_SIZE,
                function (Collection $rows): void {
                    $layoutIds = $rows->pluck('id')->all();

                    DB::transaction(function () use ($layoutIds): void {
                        DB::table('layout_modules')
                            ->whereIn('layout_id', $layoutIds)
                            ->delete();

                        DB::table('layouts')
                            ->whereIn('id', $layoutIds)
                            ->delete();
                    });
                },
                column: 'id',
            );
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
                        foreach ($this->entryRelatedTables() as $table => $fkColumn) {
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

    /**
     * @return array<string, string>
     */
    private function entryRelatedTables(): array
    {
        return array_merge(self::ENTRY_RELATED_TABLES, ModuleCatalog::entryModuleTables());
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
