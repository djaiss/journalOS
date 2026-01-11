<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\ModuleTravel;
use App\Models\User;
use App\Traits\PreventPastEntryEdits;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class LogTravel
{
    use PreventPastEntryEdits;

    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private ?string $hasTraveled,
        private ?array $travelModes,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleTravel');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $this->preventPastEditsAllowed($this->entry);

        if ($this->hasTraveled === null && $this->travelModes === null) {
            throw ValidationException::withMessages([
                'travel' => 'At least one travel value is required.',
            ]);
        }

        if ($this->hasTraveled !== null && ! in_array($this->hasTraveled, ['yes', 'no'], true)) {
            throw ValidationException::withMessages([
                'has_traveled' => 'Invalid travel status value.',
            ]);
        }

        if ($this->travelModes !== null) {
            if ($this->travelModes === []) {
                throw ValidationException::withMessages([
                    'travel_modes' => 'At least one travel mode is required.',
                ]);
            }

            foreach ($this->travelModes as $mode) {
                if (! is_string($mode) || ! in_array($mode, ModuleTravel::TRAVEL_MODES, true)) {
                    throw ValidationException::withMessages([
                        'travel_modes' => 'Invalid travel mode value.',
                    ]);
                }
            }
        }
    }

    private function log(): void
    {
        $moduleTravel = $this->entry->moduleTravel()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        if ($this->hasTraveled !== null) {
            $moduleTravel->has_traveled_today = $this->hasTraveled;
        }

        if ($this->travelModes !== null) {
            $moduleTravel->travel_mode = $this->travelModes;
        }

        $moduleTravel->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'travel_logged',
            description: 'Logged travel for ' . $this->entry->getDate(),
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
