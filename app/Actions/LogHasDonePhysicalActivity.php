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

final class LogHasDonePhysicalActivity
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $hasDonePhysicalActivity,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();

        $this->entry->has_done_physical_activity = $this->hasDonePhysicalActivity;
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

        $this->hasDonePhysicalActivity = TextSanitizer::plainText($this->hasDonePhysicalActivity);

        $messages = [];

        if ($this->hasDonePhysicalActivity === '') {
            $messages['has_done_physical_activity'] = 'Physical activity status must be plain text.';
        }

        if (mb_strlen($this->hasDonePhysicalActivity) > 255) {
            $messages['has_done_physical_activity'] = 'Physical activity status must not be longer than 255 characters.';
        }

        if ($messages === []) {
            $validValues = ['yes', 'no'];
            if (! in_array($this->hasDonePhysicalActivity, $validValues, true)) {
                $messages['has_done_physical_activity'] = 'Invalid physical activity status.';
            }
        }

        if ($messages !== []) {
            throw ValidationException::withMessages([
                'has_done_physical_activity' => $messages['has_done_physical_activity'],
            ]);
        }
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'has_done_physical_activity_logged',
            description: 'Logged physical activity status for ' . $this->entry->getDate(),
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
