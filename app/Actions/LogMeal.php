<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\ModuleMeal;
use App\Models\User;
use App\Traits\PreventPastEntryEdits;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class LogMeal
{
    use PreventPastEntryEdits;

    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private ?string $breakfast,
        private ?string $lunch,
        private ?string $dinner,
        private ?string $snack,
        private ?string $mealType,
        private ?string $socialContext,
        private ?string $notes,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleMeal');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $this->preventPastEditsAllowed($this->entry);

        if ($this->breakfast === null
            && $this->lunch === null
            && $this->dinner === null
            && $this->snack === null
            && $this->mealType === null
            && $this->socialContext === null
            && $this->notes === null
        ) {
            throw ValidationException::withMessages([
                'meal' => 'At least one meal value is required.',
            ]);
        }

        if ($this->breakfast !== null && ! in_array($this->breakfast, ModuleMeal::MEAL_PRESENCE, true)) {
            throw ValidationException::withMessages([
                'breakfast' => 'Invalid breakfast value.',
            ]);
        }

        if ($this->lunch !== null && ! in_array($this->lunch, ModuleMeal::MEAL_PRESENCE, true)) {
            throw ValidationException::withMessages([
                'lunch' => 'Invalid lunch value.',
            ]);
        }

        if ($this->dinner !== null && ! in_array($this->dinner, ModuleMeal::MEAL_PRESENCE, true)) {
            throw ValidationException::withMessages([
                'dinner' => 'Invalid dinner value.',
            ]);
        }

        if ($this->snack !== null && ! in_array($this->snack, ModuleMeal::MEAL_PRESENCE, true)) {
            throw ValidationException::withMessages([
                'snack' => 'Invalid snack value.',
            ]);
        }

        if ($this->mealType !== null && ! in_array($this->mealType, ModuleMeal::MEAL_TYPES, true)) {
            throw ValidationException::withMessages([
                'meal_type' => 'Invalid meal type value.',
            ]);
        }

        if ($this->socialContext !== null && ! in_array($this->socialContext, ModuleMeal::SOCIAL_CONTEXTS, true)) {
            throw ValidationException::withMessages([
                'social_context' => 'Invalid social context value.',
            ]);
        }
    }

    private function log(): void
    {
        $moduleMeal = $this->entry->moduleMeal()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        if ($this->breakfast !== null) {
            $moduleMeal->breakfast = $this->breakfast;
        }

        if ($this->lunch !== null) {
            $moduleMeal->lunch = $this->lunch;
        }

        if ($this->dinner !== null) {
            $moduleMeal->dinner = $this->dinner;
        }

        if ($this->snack !== null) {
            $moduleMeal->snack = $this->snack;
        }

        if ($this->mealType !== null) {
            $moduleMeal->meal_type = $this->mealType;
        }

        if ($this->socialContext !== null) {
            $moduleMeal->social_context = $this->socialContext;
        }

        if ($this->notes !== null) {
            $moduleMeal->notes = $this->notes;
        }

        $moduleMeal->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'meal_logged',
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
