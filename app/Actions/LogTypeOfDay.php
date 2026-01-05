<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Helpers\TextSanitizer;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

/**
 * This action logs the type of day for the user in this day.
 */
final class LogTypeOfDay
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $dayType,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->entry->day_type = $this->dayType;
        $this->entry->save();

        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }

        $this->dayType = TextSanitizer::plainText($this->dayType);

        if ($this->dayType === '') {
            throw new InvalidArgumentException('dayType must be plain text');
        }

        if (mb_strlen($this->dayType) > 255) {
            throw new InvalidArgumentException('dayType must not be longer than 255 characters');
        }

        if (! in_array($this->dayType, ['workday', 'day off', 'weekend', 'vacation', 'sick day'])) {
            throw new InvalidArgumentException('dayType must be one of: "workday", "day off", "weekend", "vacation", "sick day"');
        }
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'day_type_logged',
            description: 'Logged day type on ' . $this->entry->getDate(),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }

    private function refreshContentPresenceStatus(): void
    {
        CheckPresenceOfContentInJournalEntry::dispatch($this->entry)->onQueue('low');
    }
}
