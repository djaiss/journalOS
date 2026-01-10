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

final readonly class LogShoppingIntent
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private string $shoppingIntent,
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
            throw new ModelNotFoundException('Journal entry not found');
        }

        $validIntents = ['planned', 'opportunistic', 'impulse', 'replacement'];
        if (! in_array($this->shoppingIntent, $validIntents, true)) {
            throw ValidationException::withMessages([
                'shopping_intent' => 'Invalid shopping intent value.',
            ]);
        }
    }

    private function log(): void
    {
        $moduleShopping = $this->entry->moduleShopping()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        $moduleShopping->shopping_intent = $this->shoppingIntent;
        $moduleShopping->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'shopping_intent_logged',
            description: 'Logged shopping intent for ' . $this->entry->getDate(),
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
