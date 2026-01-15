<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Layout;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final readonly class SetActiveLayout
{
    public function __construct(
        private User $user,
        private Layout $layout,
    ) {}

    public function execute(): Layout
    {
        $this->validate();
        $this->deactivateOtherLayouts();
        $this->activate();
        $this->updateUserLastActivityDate();
        $this->log();

        return $this->layout;
    }

    private function validate(): void
    {
        if (! $this->layout->journal()->where('user_id', $this->user->id)->exists()) {
            throw new ModelNotFoundException('Layout not found');
        }
    }

    private function deactivateOtherLayouts(): void
    {
        Layout::query()
            ->where('journal_id', $this->layout->journal_id)
            ->update([
                'is_active' => false,
            ]);
    }

    private function activate(): void
    {
        $this->layout->update([
            'is_active' => true,
        ]);
    }

    private function log(): void
    {
        $journal = $this->layout->journal;

        LogUserAction::dispatch(
            user: $this->user,
            journal: $journal,
            action: 'layout_set_active',
            description: sprintf('Set the active layout to %s for the journal %s', $this->layout->name, $journal->name),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
