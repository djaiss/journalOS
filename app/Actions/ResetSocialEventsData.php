<?php

declare(strict_types = 1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\User;
use App\Traits\PreventPastEntryEdits;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class ResetSocialEventsData
{
    use PreventPastEntryEdits;

    public function __construct(
        private User $user,
        private JournalEntry $entry,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->reset();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleSocialEvents');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $this->preventPastEditsAllowed($this->entry);
    }

    private function reset(): void
    {
        $moduleSocialEvents = $this->entry->moduleSocialEvents;

        if ($moduleSocialEvents !== null) {
            $moduleSocialEvents->delete();
        }
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'social_events_reset',
            description: 'Reset social events for ' . $this->entry->getDate(),
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
