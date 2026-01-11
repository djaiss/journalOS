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

final readonly class LogPhysicalActivity
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private ?string $hasDonePhysicalActivity,
        private ?string $activityType,
        private ?string $activityIntensity,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('modulePhysicalActivity');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        if ($this->hasDonePhysicalActivity !== null && ! in_array($this->hasDonePhysicalActivity, ['yes', 'no'], true)) {
            throw ValidationException::withMessages([
                'has_done_physical_activity' => 'Invalid physical activity status value.',
            ]);
        }

        if ($this->activityType !== null && ! in_array($this->activityType, ['running', 'cycling', 'swimming', 'gym', 'walking'], true)) {
            throw ValidationException::withMessages([
                'activity_type' => 'Invalid activity type value.',
            ]);
        }

        if ($this->activityIntensity !== null && ! in_array($this->activityIntensity, ['light', 'moderate', 'intense'], true)) {
            throw ValidationException::withMessages([
                'activity_intensity' => 'Invalid activity intensity value.',
            ]);
        }
    }

    private function log(): void
    {
        $modulePhysicalActivity = $this->entry->modulePhysicalActivity()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        if ($this->hasDonePhysicalActivity !== null) {
            $modulePhysicalActivity->has_done_physical_activity = $this->hasDonePhysicalActivity;
        }

        if ($this->activityType !== null) {
            $modulePhysicalActivity->activity_type = $this->activityType;
        }

        if ($this->activityIntensity !== null) {
            $modulePhysicalActivity->activity_intensity = $this->activityIntensity;
        }

        $modulePhysicalActivity->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'physical_activity_logged',
            description: 'Logged physical activity for ' . $this->entry->getDate(),
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
