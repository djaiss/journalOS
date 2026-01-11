<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\ModuleHygiene;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class LogHygiene
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private ?string $showered,
        private ?string $brushedTeeth,
        private ?string $skincare,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleHygiene');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        if ($this->showered === null && $this->brushedTeeth === null && $this->skincare === null) {
            throw ValidationException::withMessages([
                'hygiene' => 'At least one hygiene value is required.',
            ]);
        }

        if ($this->showered !== null && ! in_array($this->showered, ['yes', 'no'], true)) {
            throw ValidationException::withMessages([
                'showered' => 'Invalid showered value.',
            ]);
        }

        if ($this->brushedTeeth !== null && ! in_array($this->brushedTeeth, ModuleHygiene::BRUSHED_TEETH_VALUES, true)) {
            throw ValidationException::withMessages([
                'brushed_teeth' => 'Invalid brushed teeth value.',
            ]);
        }

        if ($this->skincare !== null && ! in_array($this->skincare, ['yes', 'no'], true)) {
            throw ValidationException::withMessages([
                'skincare' => 'Invalid skincare value.',
            ]);
        }
    }

    private function log(): void
    {
        $moduleHygiene = $this->entry->moduleHygiene()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        if ($this->showered !== null) {
            $moduleHygiene->showered = $this->showered;
        }

        if ($this->brushedTeeth !== null) {
            $moduleHygiene->brushed_teeth = $this->brushedTeeth;
        }

        if ($this->skincare !== null) {
            $moduleHygiene->skincare = $this->skincare;
        }

        $moduleHygiene->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'hygiene_logged',
            description: 'Logged hygiene for ' . $this->entry->getDate(),
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
