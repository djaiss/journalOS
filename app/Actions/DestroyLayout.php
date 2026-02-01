<?php

declare(strict_types = 1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Layout;

use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;


final readonly class DestroyLayout
{
    public function __construct(
        private User $user,
        private Layout $layout,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->destroy();
        $this->updateUserLastActivityDate();
        $this->log();
    }

    private function validate(): void
    {
        if ($this->layout->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Layout not found');
        }
    }

    private function destroy(): void
    {
        $this->layout->delete();
    }

    private function log(): void
    {
        $journal = $this->layout->journal;

        LogUserAction::dispatch(
            user: $this->user,
            journal: $journal,
            action: 'layout_destroy',
            description: sprintf(
                'Deleted the layout %s for the journal %s',
                $this->layout->name,
                $journal->name,
            ),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
