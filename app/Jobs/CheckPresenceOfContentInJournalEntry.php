<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\JournalEntry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Check if the journal entry has any content in it.
 * If there is, it sets the has_content field to true, otherwise to false.
 * This job is triggered once any action is done on a journal entry.
 */
final class CheckPresenceOfContentInJournalEntry implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public JournalEntry $entry,
    ) {}

    public function handle(): void
    {
        $this->entry->loadMissing(['moduleSleep', 'moduleWork']);

        $excludedFields = [
            'id',
            'journal_id',
            'day',
            'month',
            'year',
            'has_content',
            'created_at',
            'updated_at',
        ];

        $hasContent = false;

        foreach ($this->entry->getAttributes() as $field => $value) {
            if (! in_array($field, $excludedFields) && $value !== null) {
                $hasContent = true;
                break;
            }
        }

        if (! $hasContent && $this->entry->moduleSleep !== null) {
            $moduleSleep = $this->entry->moduleSleep;
            if ($moduleSleep->bedtime !== null || $moduleSleep->wake_up_time !== null || $moduleSleep->sleep_duration_in_minutes !== null) {
                $hasContent = true;
            }
        }

        if (! $hasContent && $this->entry->moduleWork !== null) {
            $moduleWork = $this->entry->moduleWork;
            if ($moduleWork->worked !== null || $moduleWork->work_mode !== null || $moduleWork->work_load !== null || $moduleWork->work_procrastinated !== null) {
                $hasContent = true;
            }
        }

        $this->entry->has_content = $hasContent;
        $this->entry->save();
    }
}
