<?php

declare(strict_types = 1);

namespace App\Traits;

use App\Models\JournalEntry;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Date;

trait PreventPastEntryEdits
{
    private function preventPastEditsAllowed(JournalEntry $entry, int $days = 7): void
    {
        if ($entry->journal->can_edit_past === false) {
            $sevenDaysAgo = now()->subDays($days)->startOfDay();

            $entryDate = Date::create($entry->year, $entry->month, $entry->day)->startOfDay();
            if ($entryDate->lt($sevenDaysAgo)) {
                throw new ModelNotFoundException('Editing past entries is not allowed for this journal.');
            }
        }
    }
}
