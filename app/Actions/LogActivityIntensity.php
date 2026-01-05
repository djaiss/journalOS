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

final class LogActivityIntensity
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $activityIntensity,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();

        $this->entry->activity_intensity = $this->activityIntensity;
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

        $this->activityIntensity = TextSanitizer::plainText($this->activityIntensity);

        $messages = [];

        if ($this->activityIntensity === '') {
            $messages['activity_intensity'] = 'Activity intensity must be plain text.';
        }

        if (mb_strlen($this->activityIntensity) > 255) {
            $messages['activity_intensity'] = 'Activity intensity must not be longer than 255 characters.';
        }

        if ($messages === []) {
            $validIntensities = ['light', 'moderate', 'intense'];
            if (! in_array($this->activityIntensity, $validIntensities, true)) {
                $messages['activity_intensity'] = 'Invalid activity intensity.';
            }
        }

        if ($messages !== []) {
            throw ValidationException::withMessages([
                'activity_intensity' => $messages['activity_intensity'],
            ]);
        }
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'activity_intensity_logged',
            description: 'Logged activity intensity for ' . $this->entry->getDate(),
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
