<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Date;

/**
 * Log sleep habits for a journal entry.
 * Time is set using 24-hour format (HH:MM) and represented as a string.
 */
final class LogSleep
{
    public function __construct(
        private readonly User $user,
        private readonly JournalEntry $entry,
        private readonly string $bedtime,
        private readonly string $wakeUpTime,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->create();
        $this->updateUserLastActivityDate();

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }

        // check if the date is a real date
        if (! checkdate($this->month, $this->day, $this->year)) {
            throw new Exception('Invalid date');
        }

        $this->date = Date::create($this->year, $this->month, $this->day);
    }

    private function create(): void
    {
        $existingEntry = JournalEntry::query()->where('journal_id', $this->journal->id)
            ->where('day', $this->day)
            ->where('month', $this->month)
            ->where('year', $this->year)
            ->first();

        if ($existingEntry) {
            $this->entry = $existingEntry;
        } else {
            $this->entry = JournalEntry::query()->create([
                'journal_id' => $this->journal->id,
                'day' => $this->day,
                'month' => $this->month,
                'year' => $this->year,
            ]);

            $this->logUserAction();
        }
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->journal,
            action: 'entry_creation',
            description: 'Created the entry on ' . $this->date->format('l F jS, Y') . ' for the journal called ' . $this->journal->name,
        )->onQueue('low');
    }
}
