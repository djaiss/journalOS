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
use Illuminate\Validation\ValidationException;

final class LogMood
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $mood,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();

        $this->entry->mood = $this->mood;
        $this->entry->save();

        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $this->mood = TextSanitizer::plainText($this->mood);

        $messages = [];

        if ($this->mood === '') {
            $messages['mood'] = 'Mood must be plain text.';
        }

        if (mb_strlen($this->mood) > 255) {
            $messages['mood'] = 'Mood must not be longer than 255 characters.';
        }

        if ($messages === []) {
            $validMoodValues = ['terrible', 'bad', 'okay', 'good', 'great'];
            if (! in_array($this->mood, $validMoodValues, true)) {
                $messages['mood'] = 'Invalid mood value.';
            }
        }

        if ($messages !== []) {
            throw ValidationException::withMessages([
                'mood' => $messages['mood'],
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
