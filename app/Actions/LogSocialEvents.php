<?php

declare(strict_types = 1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\ModuleSocialEvents;
use App\Models\User;
use App\Traits\PreventPastEntryEdits;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class LogSocialEvents
{
    use PreventPastEntryEdits;

    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private ?string $eventType,
        private ?string $tone,
        private ?string $duration,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleSocialEvents');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $this->preventPastEditsAllowed($this->entry);

        if ($this->eventType !== null && !in_array($this->eventType, ModuleSocialEvents::EVENT_TYPE_VALUES, true)) {
            throw ValidationException::withMessages([
                'event_type' => 'Invalid social event type value.',
            ]);
        }

        if ($this->tone !== null && !in_array($this->tone, ModuleSocialEvents::TONE_VALUES, true)) {
            throw ValidationException::withMessages([
                'tone' => 'Invalid social event tone value.',
            ]);
        }

        if ($this->duration !== null && !in_array($this->duration, ModuleSocialEvents::DURATION_VALUES, true)) {
            throw ValidationException::withMessages([
                'duration' => 'Invalid social event duration value.',
            ]);
        }
    }

    private function log(): void
    {
        $moduleSocialEvents = $this->entry
            ->moduleSocialEvents()
            ->firstOrCreate(
                ['journal_entry_id' => $this->entry->id],
            );

        if ($this->eventType !== null) {
            $moduleSocialEvents->event_type = $this->eventType;
        }

        if ($this->tone !== null) {
            $moduleSocialEvents->tone = $this->tone;
        }

        if ($this->duration !== null) {
            $moduleSocialEvents->duration = $this->duration;
        }

        $moduleSocialEvents->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'social_events_logged',
            description: 'Logged social events for ' . $this->entry->getDate(),
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
