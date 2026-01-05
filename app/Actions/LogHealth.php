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

final class LogHealth
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $health,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();

        $this->entry->health = $this->health;
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

        $this->health = TextSanitizer::plainText($this->health);

        $messages = [];

        if ($this->health === '') {
            $messages['health'] = 'Health must be plain text.';
        }

        if (mb_strlen($this->health) > 255) {
            $messages['health'] = 'Health must not be longer than 255 characters.';
        }

        if ($messages === []) {
            $validHealthValues = ['good', 'okay', 'not great'];
            if (! in_array($this->health, $validHealthValues, true)) {
                $messages['health'] = 'Invalid health value.';
            }
        }

        if ($messages !== []) {
            throw ValidationException::withMessages([
                'health' => $messages['health'],
            ]);
        }
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'health_logged',
            description: 'Logged health for ' . $this->entry->getDate(),
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
