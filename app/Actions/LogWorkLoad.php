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
 * This action logs the work load for the user in this day.
 */
final class LogWorkLoad
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $workLoad,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->entry->work_load = $this->workLoad;
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

        $this->workLoad = TextSanitizer::plainText($this->workLoad);

        if ($this->workLoad === '') {
            throw new InvalidArgumentException('workLoad must be plain text');
        }

        if (mb_strlen($this->workLoad) > 255) {
            throw new InvalidArgumentException('workLoad must not be longer than 255 characters');
        }

        if (! in_array($this->workLoad, ['light', 'medium', 'heavy'])) {
            throw new InvalidArgumentException('workLoad must be either "light", "medium", or "heavy"');
        }
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'work_load_logged',
            description: 'Logged work load on ' . $this->entry->getDate(),
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
