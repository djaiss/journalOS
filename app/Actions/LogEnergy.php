<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Helpers\TextSanitizer;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final class LogEnergy
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $energy,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();

        $this->entry->energy = $this->energy;
        $this->entry->save();

        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        $this->energy = TextSanitizer::plainText($this->energy);

        $messages = [];

        if ($this->energy === '') {
            $messages['energy'] = 'Energy must be plain text.';
        }

        if (mb_strlen($this->energy) > 255) {
            $messages['energy'] = 'Energy must not be longer than 255 characters.';
        }

        if ($messages === []) {
            $validEnergyValues = ['very low', 'low', 'normal', 'high', 'very high'];
            if (! in_array($this->energy, $validEnergyValues, true)) {
                $messages['energy'] = 'Invalid energy value.';
            }
        }

        if ($messages !== []) {
            throw ValidationException::withMessages([
                'energy' => $messages['energy'],
            ]);
        }
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'energy_logged',
            description: 'Logged energy for ' . $this->entry->getDate(),
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
