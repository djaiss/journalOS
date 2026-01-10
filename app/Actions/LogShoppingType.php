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
 * This action logs the shopping types for this day.
 */
final readonly class LogShoppingType
{
    private const array VALID_TYPES = [
        'groceries',
        'clothes',
        'electronics_tech',
        'household_essentials',
        'books_media',
        'gifts',
        'online_shopping',
        'other',
    ];

    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private array $shoppingTypes,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();
        $this->log();

        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        $this->entry->load('moduleShopping');

        return $this->entry;
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }

        if (empty($this->shoppingTypes)) {
            throw new InvalidArgumentException('shoppingTypes cannot be empty');
        }

        foreach ($this->shoppingTypes as $type) {
            if (! in_array($type, self::VALID_TYPES, true)) {
                throw new InvalidArgumentException('Each shoppingType must be one of: ' . implode(', ', self::VALID_TYPES));
            }
        }
    }

    private function log(): void
    {
        $moduleShopping = $this->entry->moduleShopping()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        $moduleShopping->shopping_type = $this->shoppingTypes;
        $moduleShopping->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'shopping_type_logged',
            description: 'Logged shopping type on ' . $this->entry->getDate(),
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
