<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\ModuleShopping;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final readonly class LogShopping
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private ?string $hasShopped,
        private ?array $shoppingTypes,
        private ?string $shoppingIntent,
        private ?string $shoppingContext,
        private ?string $shoppingFor,
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

        if ($this->hasShopped === null
            && $this->shoppingTypes === null
            && $this->shoppingIntent === null
            && $this->shoppingContext === null
            && $this->shoppingFor === null
        ) {
            throw ValidationException::withMessages([
                'shopping' => 'At least one shopping value is required.',
            ]);
        }

        if ($this->hasShopped !== null && ! in_array($this->hasShopped, ['yes', 'no'], true)) {
            throw ValidationException::withMessages([
                'has_shopped' => 'Invalid shopping status value.',
            ]);
        }

        if ($this->shoppingTypes !== null) {
            if ($this->shoppingTypes === []) {
                throw ValidationException::withMessages([
                    'shopping_types' => 'At least one shopping type is required.',
                ]);
            }

            foreach ($this->shoppingTypes as $type) {
                if (! is_string($type) || ! in_array($type, ModuleShopping::SHOPPING_TYPES, true)) {
                    throw ValidationException::withMessages([
                        'shopping_types' => 'Invalid shopping type value.',
                    ]);
                }
            }
        }

        if ($this->shoppingIntent !== null && ! in_array($this->shoppingIntent, ModuleShopping::SHOPPING_INTENTS, true)) {
            throw ValidationException::withMessages([
                'shopping_intent' => 'Invalid shopping intent value.',
            ]);
        }

        if ($this->shoppingContext !== null && ! in_array($this->shoppingContext, ModuleShopping::SHOPPING_CONTEXTS, true)) {
            throw ValidationException::withMessages([
                'shopping_context' => 'Invalid shopping context value.',
            ]);
        }

        if ($this->shoppingFor !== null && ! in_array($this->shoppingFor, ModuleShopping::SHOPPING_FOR_OPTIONS, true)) {
            throw ValidationException::withMessages([
                'shopping_for' => 'Invalid shopping for value.',
            ]);
        }
    }

    private function log(): void
    {
        $moduleShopping = $this->entry->moduleShopping()->firstOrCreate(
            ['journal_entry_id' => $this->entry->id],
        );

        if ($this->hasShopped !== null) {
            $moduleShopping->has_shopped_today = $this->hasShopped;
        }

        if ($this->shoppingTypes !== null) {
            $moduleShopping->shopping_type = $this->shoppingTypes;
        }

        if ($this->shoppingIntent !== null) {
            $moduleShopping->shopping_intent = $this->shoppingIntent;
        }

        if ($this->shoppingContext !== null) {
            $moduleShopping->shopping_context = $this->shoppingContext;
        }

        if ($this->shoppingFor !== null) {
            $moduleShopping->shopping_for = $this->shoppingFor;
        }

        $moduleShopping->save();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'shopping_logged',
            description: 'Logged shopping for ' . $this->entry->getDate(),
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
