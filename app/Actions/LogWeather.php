<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\ModuleWeather;
use App\Models\User;
use App\Traits\PreventPastEntryEdits;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class LogWeather
{
    use PreventPastEntryEdits;

    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private ?string $condition,
        private ?string $temperatureRange,
        private ?string $precipitation,
        private ?string $daylight,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleWeather');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $this->preventPastEditsAllowed($this->entry);

        if ($this->condition === null
            && $this->temperatureRange === null
            && $this->precipitation === null
            && $this->daylight === null
        ) {
            throw ValidationException::withMessages([
                'weather' => 'At least one weather value is required.',
            ]);
        }

        if ($this->condition !== null && ! in_array($this->condition, ModuleWeather::CONDITIONS, true)) {
            throw ValidationException::withMessages([
                'condition' => 'Invalid condition value.',
            ]);
        }

        if ($this->temperatureRange !== null && ! in_array($this->temperatureRange, ModuleWeather::TEMPERATURE_RANGES, true)) {
            throw ValidationException::withMessages([
                'temperature_range' => 'Invalid temperature range value.',
            ]);
        }

        if ($this->precipitation !== null && ! in_array($this->precipitation, ModuleWeather::PRECIPITATION_LEVELS, true)) {
            throw ValidationException::withMessages([
                'precipitation' => 'Invalid precipitation value.',
            ]);
        }

        if ($this->daylight !== null && ! in_array($this->daylight, ModuleWeather::DAYLIGHT_VALUES, true)) {
            throw ValidationException::withMessages([
                'daylight' => 'Invalid daylight value.',
            ]);
        }
    }

    private function log(): void
    {
        $moduleWeather = $this->entry->moduleWeather()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        if ($this->condition !== null) {
            $moduleWeather->condition = $this->condition;
        }

        if ($this->temperatureRange !== null) {
            $moduleWeather->temperature_range = $this->temperatureRange;
        }

        if ($this->precipitation !== null) {
            $moduleWeather->precipitation = $this->precipitation;
        }

        if ($this->daylight !== null) {
            $moduleWeather->daylight = $this->daylight;
        }

        $moduleWeather->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'weather_logged',
            description: 'Logged weather for ' . $this->entry->getDate(),
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
