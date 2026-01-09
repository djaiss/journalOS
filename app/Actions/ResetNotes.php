<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class ResetNotes
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->save();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        return $this->entry->fresh();
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }
    }

    private function save(): void
    {
        $this->entry->richTextNotes()->updateOrCreate(
            ['field' => 'notes'],
            ['body' => ''],
        );
        $this->entry->touch();
        $this->entry->unsetRelation('richTextNotes');
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'notes_reset',
            description: 'Reset notes for journal entry on ' . $this->entry->getDate(),
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
