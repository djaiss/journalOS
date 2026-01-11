<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\ModuleSexualActivity;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class LogSexualActivity
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private ?string $hadSexualActivity,
        private ?string $sexualActivityType,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleSexualActivity');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        if ($this->hadSexualActivity === null && $this->sexualActivityType === null) {
            throw ValidationException::withMessages([
                'sexual_activity' => 'At least one sexual activity value is required.',
            ]);
        }

        if ($this->hadSexualActivity !== null && ! in_array($this->hadSexualActivity, ['yes', 'no'], true)) {
            throw ValidationException::withMessages([
                'had_sexual_activity' => 'Invalid sexual activity status value.',
            ]);
        }

        if ($this->sexualActivityType !== null && ! in_array($this->sexualActivityType, ModuleSexualActivity::SEXUAL_ACTIVITY_TYPES, true)) {
            throw ValidationException::withMessages([
                'sexual_activity_type' => 'Invalid sexual activity type value.',
            ]);
        }
    }

    private function log(): void
    {
        $moduleSexualActivity = $this->entry->moduleSexualActivity()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        if ($this->hadSexualActivity !== null) {
            $moduleSexualActivity->had_sexual_activity = $this->hadSexualActivity;
        }

        if ($this->sexualActivityType !== null) {
            $moduleSexualActivity->sexual_activity_type = $this->sexualActivityType;
        }

        $moduleSexualActivity->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'sexual_activity_logged',
            description: 'Logged sexual activity for ' . $this->entry->getDate(),
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
