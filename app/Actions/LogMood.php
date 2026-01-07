<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class LogMood
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $mood,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();

        $moduleMood = $this->entry->moduleMood()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        $moduleMood->mood = $this->mood;
        $moduleMood->save();

        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleMood');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $validMoodValues = ['terrible', 'bad', 'okay', 'good', 'great'];
        if (! in_array($this->mood, $validMoodValues, true)) {
            throw ValidationException::withMessages([
                'mood' => 'Invalid mood value.',
            ]);
        }
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'mood_logged',
            description: 'Logged mood for ' . $this->entry->getDate(),
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
