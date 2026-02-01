<?php

declare(strict_types = 1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\ModuleWeatherInfluence;
use App\Models\User;
use App\Traits\PreventPastEntryEdits;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class LogWeatherInfluence
{
    use PreventPastEntryEdits;

    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private ?string $moodEffect,
        private ?string $energyEffect,
        private ?string $plansInfluence,
        private ?string $outsideTime,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleWeatherInfluence');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $this->preventPastEditsAllowed($this->entry);

        if (
            $this->moodEffect === null
            && $this->energyEffect === null
            && $this->plansInfluence === null
            && $this->outsideTime === null
        ) {
            throw ValidationException::withMessages([
                'weather_influence' => 'At least one weather influence value is required.',
            ]);
        }

        if ($this->moodEffect !== null && !in_array($this->moodEffect, ModuleWeatherInfluence::MOOD_EFFECTS, true)) {
            throw ValidationException::withMessages([
                'mood_effect' => 'Invalid mood effect value.',
            ]);
        }

        if (
            $this->energyEffect !== null
            && !in_array($this->energyEffect, ModuleWeatherInfluence::ENERGY_EFFECTS, true)
        ) {
            throw ValidationException::withMessages([
                'energy_effect' => 'Invalid energy effect value.',
            ]);
        }

        if (
            $this->plansInfluence !== null
            && !in_array($this->plansInfluence, ModuleWeatherInfluence::PLANS_INFLUENCES, true)
        ) {
            throw ValidationException::withMessages([
                'plans_influence' => 'Invalid plans influence value.',
            ]);
        }

        if ($this->outsideTime !== null && !in_array($this->outsideTime, ModuleWeatherInfluence::OUTSIDE_TIMES, true)) {
            throw ValidationException::withMessages([
                'outside_time' => 'Invalid outside time value.',
            ]);
        }
    }

    private function log(): void
    {
        $moduleWeatherInfluence = $this->entry
            ->moduleWeatherInfluence()
            ->firstOrCreate(
                ['journal_entry_id' => $this->entry->id],
            );

        if ($this->moodEffect !== null) {
            $moduleWeatherInfluence->mood_effect = $this->moodEffect;
        }

        if ($this->energyEffect !== null) {
            $moduleWeatherInfluence->energy_effect = $this->energyEffect;
        }

        if ($this->plansInfluence !== null) {
            $moduleWeatherInfluence->plans_influence = $this->plansInfluence;
        }

        if ($this->outsideTime !== null) {
            $moduleWeatherInfluence->outside_time = $this->outsideTime;
        }

        $moduleWeatherInfluence->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'weather_influence_logged',
            description: 'Logged weather influence for ' . $this->entry->getDate(),
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
