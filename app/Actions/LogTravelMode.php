<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;

/**
 * This action logs the travel mode used by the user in this day.
 */
final readonly class LogTravelMode
{
    private const array VALID_MODES = ['car', 'plane', 'train', 'bike', 'bus', 'walk', 'boat', 'other'];

    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $travelMode,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->entry->travel_mode = $this->travelMode;
        $this->entry->save();

        $this->logUserAction();
        $this->updateUserLastActivityDate();

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }

        if (! in_array($this->travelMode, self::VALID_MODES, true)) {
            throw new InvalidArgumentException('travelMode must be one of: ' . implode(', ', self::VALID_MODES));
        }
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'travel_mode_logged',
            description: 'Logged travel mode on ' . $this->entry->getDate(),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
