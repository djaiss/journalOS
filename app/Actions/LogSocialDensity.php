<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class LogSocialDensity
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $socialDensity,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();

        $moduleSocialDensity = $this->entry->moduleSocialDensity()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        $moduleSocialDensity->social_density = $this->socialDensity;
        $moduleSocialDensity->save();

        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleSocialDensity');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $validSocialDensityValues = ['alone', 'few people', 'crowd', 'too much'];
        if (! in_array($this->socialDensity, $validSocialDensityValues, true)) {
            throw ValidationException::withMessages([
                'social_density' => 'Invalid social density value.',
            ]);
        }
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'social_density_logged',
            description: 'Logged social density for ' . $this->entry->getDate(),
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
