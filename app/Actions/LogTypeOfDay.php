<?php

declare(strict_types = 1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\ModuleDayType;
use App\Models\User;
use App\Traits\PreventPastEntryEdits;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

/**
 * This action logs the type of day for the user in this day.
 */
final readonly class LogTypeOfDay
{
    use PreventPastEntryEdits;

    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $dayType,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleDayType');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }

        $this->preventPastEditsAllowed($this->entry);

        if (!in_array($this->dayType, ModuleDayType::DAY_TYPES, true)) {
            $dayTypes = implode('", "', ModuleDayType::DAY_TYPES);

            throw new InvalidArgumentException('dayType must be one of: "' . $dayTypes . '"');
        }
    }

    private function log(): void
    {
        $moduleDayType = $this->entry
            ->moduleDayType()
            ->firstOrCreate(
                ['journal_entry_id' => $this->entry->id],
            );

        $moduleDayType->day_type = $this->dayType;
        $moduleDayType->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'day_type_logged',
            description: 'Logged day type on ' . $this->entry->getDate(),
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
