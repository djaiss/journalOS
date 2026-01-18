<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class ResetDayData
{
    public function __construct(
        private User $user,
        private JournalEntry $entry,
    ) {}

    public function execute(): JournalEntry
    {
        $this->validate();

        $this->entry->load([
            'moduleSleep',
            'moduleWork',
            'moduleTravel',
            'moduleShopping',
            'moduleKids',
            'moduleDayType',
            'modulePrimaryObligation',
            'modulePhysicalActivity',
            'moduleHealth',
            'moduleHygiene',
            'moduleMood',
            'moduleSexualActivity',
            'moduleEnergy',
            'moduleSocialDensity',
            'richTextNotes',
        ]);

        $this->resetModules();
        $this->resetNotes();
        $this->resetBooks();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();

        return $this->entry->fresh([
            'moduleSleep',
            'moduleWork',
            'moduleTravel',
            'moduleShopping',
            'moduleKids',
            'moduleDayType',
            'modulePrimaryObligation',
            'modulePhysicalActivity',
            'moduleHealth',
            'moduleHygiene',
            'moduleMood',
            'moduleSexualActivity',
            'moduleEnergy',
            'moduleSocialDensity',
            'richTextNotes',
            'books',
        ]);
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }
    }

    private function resetModules(): void
    {
        $modules = [
            $this->entry->moduleSleep,
            $this->entry->moduleWork,
            $this->entry->moduleTravel,
            $this->entry->moduleShopping,
            $this->entry->moduleKids,
            $this->entry->moduleDayType,
            $this->entry->modulePrimaryObligation,
            $this->entry->modulePhysicalActivity,
            $this->entry->moduleHealth,
            $this->entry->moduleHygiene,
            $this->entry->moduleMood,
            $this->entry->moduleSexualActivity,
            $this->entry->moduleEnergy,
            $this->entry->moduleSocialDensity,
        ];

        foreach ($modules as $module) {
            if ($module !== null) {
                $module->delete();
            }
        }
    }

    private function resetNotes(): void
    {
        $this->entry->richTextNotes()->updateOrCreate(
            ['field' => 'notes'],
            ['body' => ''],
        );
        $this->entry->touch();
        $this->entry->unsetRelation('richTextNotes');
    }

    private function resetBooks(): void
    {
        $this->entry->books()->detach();
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'day_data_reset',
            description: 'Reset day data for journal entry on ' . $this->entry->getDate(),
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
