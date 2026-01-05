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
 * This action logs whether the user had the kids today.
 */
final class LogHadKidsToday
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $hadKidsToday,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();

        $this->entry->had_kids_today = $this->hadKidsToday;
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

        $this->hadKidsToday = TextSanitizer::plainText($this->hadKidsToday);

        if ($this->hadKidsToday === '') {
            throw new InvalidArgumentException('hadKidsToday must be plain text');
        }

        if (mb_strlen($this->hadKidsToday) > 255) {
            throw new InvalidArgumentException('hadKidsToday must not be longer than 255 characters');
        }

        if ($this->hadKidsToday !== 'yes' && $this->hadKidsToday !== 'no') {
            throw new InvalidArgumentException('hadKidsToday must be either "yes" or "no"');
        }
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'kids_today_logged',
            description: 'Logged if you had the kids today on ' . $this->entry->getDate(),
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
