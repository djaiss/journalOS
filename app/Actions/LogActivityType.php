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

final readonly class LogActivityType
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $activityType,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();

        $this->entry->activity_type = $this->activityType;
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

        $validTypes = ['running', 'cycling', 'swimming', 'gym', 'walking'];
        if (!in_array($this->activityType, $validTypes)) {
            throw ValidationException::withMessages([
                'activity_type' => 'Invalid activity type.',
            ]);
        }
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'activity_type_logged',
            description: 'Logged activity type for ' . $this->entry->getDate(),
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
