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
 * This action logs whether the user had sexual activity on this day.
 */
final class LogHadSexualActivity
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $hadSexualActivity,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->entry->had_sexual_activity = $this->hadSexualActivity;
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

        $this->hadSexualActivity = TextSanitizer::plainText($this->hadSexualActivity);

        if ($this->hadSexualActivity === '') {
            throw new InvalidArgumentException('hadSexualActivity must be plain text');
        }

        if (mb_strlen($this->hadSexualActivity) > 255) {
            throw new InvalidArgumentException('hadSexualActivity must not be longer than 255 characters');
        }

        if ($this->hadSexualActivity !== 'yes' && $this->hadSexualActivity !== 'no') {
            throw new InvalidArgumentException('hadSexualActivity must be either "yes" or "no"');
        }
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'sexual_activity_logged',
            description: 'Logged sexual activity on ' . $this->entry->getDate(),
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
