<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

/**
 * This action logs whether the user shopped or not in this day.
 */
final readonly class LogShoppedToday
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $hasShopped,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();

        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleShopping');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }

        if ($this->hasShopped !== 'yes' && $this->hasShopped !== 'no') {
            throw new InvalidArgumentException('hasShopped must be either "yes" or "no"');
        }
    }

    private function log(): void
    {
        $moduleShopping = $this->entry->moduleShopping()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        $moduleShopping->has_shopped_today = $this->hasShopped;
        $moduleShopping->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'has_shopped_logged',
            description: 'Logged if you shopped on ' . $this->entry->getDate(),
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
