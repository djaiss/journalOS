<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class LogActivityIntensity
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

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $validIntensities = ['light', 'moderate', 'intense'];
        if (!in_array($this->activityIntensity, $validIntensities)) {
            throw ValidationException::withMessages([
                'activity_intensity' => 'Invalid activity intensity.',
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
}
