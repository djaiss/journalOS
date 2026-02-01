<?php

declare(strict_types = 1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\ModuleMeals;
use App\Models\User;
use App\Traits\PreventPastEntryEdits;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class LogMeals
{
    use PreventPastEntryEdits;

    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private ?array $mealPresence,
        private ?string $mealType,
        private ?string $socialContext,
        private ?string $hasNotes,
        private ?string $notes,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleMeals');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $this->preventPastEditsAllowed($this->entry);

        if (
            $this->mealPresence === null
            && $this->mealType === null
            && $this->socialContext === null
            && $this->hasNotes === null
            && $this->notes === null
        ) {
            throw ValidationException::withMessages([
                'meals' => 'At least one meal value is required.',
            ]);
        }

        if ($this->mealPresence !== null) {
            if ($this->mealPresence === []) {
                throw ValidationException::withMessages([
                    'meal_presence' => 'At least one meal presence value is required.',
                ]);
            }

            foreach ($this->mealPresence as $presence) {
                if (!( !is_string($presence) || !in_array($presence, ModuleMeals::MEAL_PRESENCE, true) )) {
                    continue;
                }

                throw ValidationException::withMessages([
                    'meal_presence' => 'Invalid meal presence value.',
                ]);
            }
        }

        if ($this->mealType !== null && !in_array($this->mealType, ModuleMeals::MEAL_TYPES, true)) {
            throw ValidationException::withMessages([
                'meal_type' => 'Invalid meal type value.',
            ]);
        }

        if ($this->socialContext !== null && !in_array($this->socialContext, ModuleMeals::SOCIAL_CONTEXTS, true)) {
            throw ValidationException::withMessages([
                'social_context' => 'Invalid social context value.',
            ]);
        }

        if ($this->hasNotes !== null && !in_array($this->hasNotes, ['yes', 'no'], true)) {
            throw ValidationException::withMessages([
                'has_notes' => 'Invalid notes toggle value.',
            ]);
        }

        if ($this->hasNotes === 'no' && $this->notes !== null) {
            throw ValidationException::withMessages([
                'notes' => 'Notes must be empty when notes are disabled.',
            ]);
        }
    }

    private function log(): void
    {
        $moduleMeals = $this->entry
            ->moduleMeals()
            ->firstOrCreate(
                ['journal_entry_id' => $this->entry->id],
            );

        if ($this->mealPresence !== null) {
            $moduleMeals->meal_presence = $this->mealPresence;
        }

        if ($this->mealType !== null) {
            $moduleMeals->meal_type = $this->mealType;
        }

        if ($this->socialContext !== null) {
            $moduleMeals->social_context = $this->socialContext;
        }

        if ($this->hasNotes !== null) {
            $moduleMeals->has_notes = $this->hasNotes;
        }

        if ($this->notes !== null) {
            $moduleMeals->notes = $this->notes;
            if ($this->hasNotes === null) {
                $moduleMeals->has_notes = 'yes';
            }
        }

        if ($this->hasNotes === 'no') {
            $moduleMeals->notes = null;
        }

        $moduleMeals->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'meals_logged',
            description: 'Logged meals for ' . $this->entry->getDate(),
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
