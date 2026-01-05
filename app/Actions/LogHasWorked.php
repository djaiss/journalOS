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
 * This action logs whether the user has worked or not in this day.
 */
final class LogHasWorked
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $hasWorked,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->entry->worked = $this->hasWorked;
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

        $this->hasWorked = TextSanitizer::plainText($this->hasWorked);

        if ($this->hasWorked === '') {
            throw new InvalidArgumentException('hasWorked must be plain text');
        }

        if (mb_strlen($this->hasWorked) > 255) {
            throw new InvalidArgumentException('hasWorked must not be longer than 255 characters');
        }

        if ($this->hasWorked !== 'yes' && $this->hasWorked !== 'no') {
            throw new InvalidArgumentException('hasWorked must be either "yes" or "no"');
        }
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'has_worked_logged',
            description: 'Logged if you have worked on ' . $this->entry->getDate(),
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
