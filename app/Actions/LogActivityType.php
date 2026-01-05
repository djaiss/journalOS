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

final class LogActivityType
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

        $this->activityType = TextSanitizer::plainText($this->activityType);

        $messages = [];

        if ($this->activityType === '') {
            $messages['activity_type'] = 'Activity type must be plain text.';
        }

        if (mb_strlen($this->activityType) > 255) {
            $messages['activity_type'] = 'Activity type must not be longer than 255 characters.';
        }

        if ($messages === []) {
            $validTypes = ['running', 'cycling', 'swimming', 'gym', 'walking'];
            if (! in_array($this->activityType, $validTypes, true)) {
                $messages['activity_type'] = 'Invalid activity type.';
            }
        }

        if ($messages !== []) {
            throw ValidationException::withMessages([
                'activity_type' => $messages['activity_type'],
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
