<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

trait PreventPastEntryEdits
{
    private function preventPastEditsAllowed(JournalEntry $entry, int $days = 7): void
    {
        if ($this->entry->journal->can_edit_past === false) {
            $sevenDaysAgo = now()->subDays(7)->startOfDay();
            $entryDate = Carbon::create($this->entry->year, $this->entry->month, $this->entry->day)->startOfDay();
            if ($entryDate->lt($sevenDaysAgo)) {
                throw new ModelNotFoundException('Editing past entries is not allowed for this journal.');
            }
        }
    }
}
