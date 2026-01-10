<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

/**
 * This action logs the type of sexual activity for the user on this day.
 */
final readonly class LogSexualActivityType
{
    private const array VALID_TYPES = ['solo', 'with-partner', 'intimate-contact'];

    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $sexualActivityType,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleSexualActivity');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }

        if (! in_array($this->sexualActivityType, self::VALID_TYPES, true)) {
            throw new InvalidArgumentException('sexualActivityType must be one of: ' . implode(', ', self::VALID_TYPES));
        }
    }

    private function log(): void
    {
        $moduleSexualActivity = $this->entry->moduleSexualActivity()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        $moduleSexualActivity->sexual_activity_type = $this->sexualActivityType;
        $moduleSexualActivity->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'sexual_activity_type_logged',
            description: 'Logged sexual activity type on ' . $this->entry->getDate(),
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
