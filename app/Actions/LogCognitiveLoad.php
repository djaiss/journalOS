<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\ModuleCognitiveLoad;
use App\Models\User;
use App\Traits\PreventPastEntryEdits;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class LogCognitiveLoad
{
    use PreventPastEntryEdits;

    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $cognitiveLoad,
        private ?string $primarySource,
        private ?string $loadQuality,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleCognitiveLoad');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $this->preventPastEditsAllowed($this->entry);

        if (! in_array($this->cognitiveLoad, ModuleCognitiveLoad::COGNITIVE_LOAD_LEVELS, true)) {
            throw ValidationException::withMessages([
                'cognitive_load' => 'Invalid cognitive load value.',
            ]);
        }

        if ($this->primarySource !== null && ! in_array($this->primarySource, ModuleCognitiveLoad::PRIMARY_SOURCES, true)) {
            throw ValidationException::withMessages([
                'primary_source' => 'Invalid primary source value.',
            ]);
        }

        if ($this->loadQuality !== null && ! in_array($this->loadQuality, ModuleCognitiveLoad::LOAD_QUALITIES, true)) {
            throw ValidationException::withMessages([
                'load_quality' => 'Invalid load quality value.',
            ]);
        }
    }

    private function log(): void
    {
        $moduleCognitiveLoad = $this->entry->moduleCognitiveLoad()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        $moduleCognitiveLoad->cognitive_load = $this->cognitiveLoad;

        if ($this->primarySource !== null) {
            $moduleCognitiveLoad->primary_source = $this->primarySource;
        }

        if ($this->loadQuality !== null) {
            $moduleCognitiveLoad->load_quality = $this->loadQuality;
        }

        $moduleCognitiveLoad->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'cognitive_load_logged',
            description: 'Logged cognitive load for ' . $this->entry->getDate(),
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
