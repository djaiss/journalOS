<?php

declare(strict_types = 1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\ModuleWork;
use App\Models\User;
use App\Traits\PreventPastEntryEdits;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class LogWork
{
    use PreventPastEntryEdits;

    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private ?string $worked,
        private ?string $workMode,
        private ?string $workLoad,
        private ?string $workProcrastinated,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleWork');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $this->preventPastEditsAllowed($this->entry);

        if (
            $this->worked === null
            && $this->workMode === null
            && $this->workLoad === null
            && $this->workProcrastinated === null
        ) {
            throw ValidationException::withMessages([
                'work' => 'At least one work value is required.',
            ]);
        }

        if ($this->worked !== null && !in_array($this->worked, ['yes', 'no'], true)) {
            throw ValidationException::withMessages([
                'worked' => 'Invalid worked value.',
            ]);
        }

        if ($this->workMode !== null && !in_array($this->workMode, ModuleWork::WORK_MODES, true)) {
            throw ValidationException::withMessages([
                'work_mode' => 'Invalid work mode value.',
            ]);
        }

        if ($this->workLoad !== null && !in_array($this->workLoad, ModuleWork::WORK_LOADS, true)) {
            throw ValidationException::withMessages([
                'work_load' => 'Invalid work load value.',
            ]);
        }

        if ($this->workProcrastinated !== null && !in_array($this->workProcrastinated, ['yes', 'no'], true)) {
            throw ValidationException::withMessages([
                'work_procrastinated' => 'Invalid work procrastinated value.',
            ]);
        }
    }

    private function log(): void
    {
        $moduleWork = $this->entry
            ->moduleWork()
            ->firstOrCreate(
                ['journal_entry_id' => $this->entry->id],
            );

        if ($this->worked !== null) {
            $moduleWork->worked = $this->worked;
        }

        if ($this->workMode !== null) {
            $moduleWork->work_mode = $this->workMode;
        }

        if ($this->workLoad !== null) {
            $moduleWork->work_load = $this->workLoad;
        }

        if ($this->workProcrastinated !== null) {
            $moduleWork->work_procrastinated = $this->workProcrastinated;
        }

        $moduleWork->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'work_logged',
            description: 'Logged work for ' . $this->entry->getDate(),
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
