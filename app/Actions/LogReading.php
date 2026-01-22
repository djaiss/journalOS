<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\ModuleReading;
use App\Models\User;
use App\Traits\PreventPastEntryEdits;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class LogReading
{
    use PreventPastEntryEdits;

    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private ?string $didReadToday,
        private ?string $readingAmount,
        private ?string $mentalState,
        private ?string $readingFeel,
        private ?string $wantContinue,
        private ?string $readingLimit,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleReading', 'books');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $this->preventPastEditsAllowed($this->entry);

        if ($this->didReadToday === null
            && $this->readingAmount === null
            && $this->mentalState === null
            && $this->readingFeel === null
            && $this->wantContinue === null
            && $this->readingLimit === null
        ) {
            throw ValidationException::withMessages([
                'reading' => 'At least one reading value is required.',
            ]);
        }

        if ($this->didReadToday !== null && ! in_array($this->didReadToday, ['yes', 'no'], true)) {
            throw ValidationException::withMessages([
                'did_read_today' => 'Invalid reading value.',
            ]);
        }

        if ($this->readingAmount !== null && ! in_array($this->readingAmount, ModuleReading::READING_AMOUNTS, true)) {
            throw ValidationException::withMessages([
                'reading_amount' => 'Invalid reading amount value.',
            ]);
        }

        if ($this->mentalState !== null && ! in_array($this->mentalState, ModuleReading::MENTAL_STATES, true)) {
            throw ValidationException::withMessages([
                'mental_state' => 'Invalid mental state value.',
            ]);
        }

        if ($this->readingFeel !== null && ! in_array($this->readingFeel, ModuleReading::READING_FEELS, true)) {
            throw ValidationException::withMessages([
                'reading_feel' => 'Invalid reading feel value.',
            ]);
        }

        if ($this->wantContinue !== null && ! in_array($this->wantContinue, ModuleReading::WANT_CONTINUE_OPTIONS, true)) {
            throw ValidationException::withMessages([
                'want_continue' => 'Invalid reading continuation value.',
            ]);
        }

        if ($this->readingLimit !== null && ! in_array($this->readingLimit, ModuleReading::READING_LIMITS, true)) {
            throw ValidationException::withMessages([
                'reading_limit' => 'Invalid reading limit value.',
            ]);
        }
    }

    private function log(): void
    {
        $moduleReading = $this->entry->moduleReading()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        if ($this->didReadToday !== null) {
            $moduleReading->did_read_today = $this->didReadToday;
        }

        if ($this->didReadToday === 'no') {
            $moduleReading->reading_amount = null;
            $moduleReading->mental_state = null;
            $moduleReading->reading_feel = null;
            $moduleReading->want_continue = null;
            $moduleReading->reading_limit = null;
            $moduleReading->save();

            $this->entry->books()->detach();

            return;
        }

        if ($this->readingAmount !== null) {
            $moduleReading->reading_amount = $this->readingAmount;
        }

        if ($this->mentalState !== null) {
            $moduleReading->mental_state = $this->mentalState;
        }

        if ($this->readingFeel !== null) {
            $moduleReading->reading_feel = $this->readingFeel;
        }

        if ($this->wantContinue !== null) {
            $moduleReading->want_continue = $this->wantContinue;
        }

        if ($this->readingLimit !== null) {
            $moduleReading->reading_limit = $this->readingLimit;
        }

        $moduleReading->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'reading_logged',
            description: 'Logged reading for ' . $this->entry->getDate(),
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
