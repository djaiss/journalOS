<?php

declare(strict_types = 1);

namespace App\Actions;

use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\Layout;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;

final class CreateLayout
{
    private Layout $layout;

    public function __construct(
        private readonly User $user,
        private readonly Journal $journal,
        private string $name,
        private readonly int $columnsCount,
    ) {}

    public function execute(): Layout
    {
        $this->validate();
        $this->create();
        $this->updateUserLastActivityDate();
        $this->log();

        return $this->layout;
    }

    private function validate(): void
    {
        $this->name = TextSanitizer::plainText($this->name);

        if ($this->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }

        if ($this->name === '') {
            throw ValidationException::withMessages([
                'layout_name' => 'Layout name can only contain letters, numbers, spaces, hyphens and underscores',
            ]);
        }

        if (in_array(preg_match('/^[a-zA-Z0-9\s\-_]+$/', $this->name), [0, false], true)) {
            throw ValidationException::withMessages([
                'layout_name' => 'Layout name can only contain letters, numbers, spaces, hyphens and underscores',
            ]);
        }

        if ($this->columnsCount < 1 || $this->columnsCount > 4) {
            throw ValidationException::withMessages([
                'columns_count' => 'Columns count must be between 1 and 4',
            ]);
        }
    }

    private function create(): void
    {
        $this->layout = Layout::query()->create([
            'journal_id' => $this->journal->id,
            'name' => $this->name,
            'columns_count' => $this->columnsCount,
            'is_active' => false,
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->journal,
            action: 'layout_creation',
            description: sprintf('Created the layout %s for the journal %s', $this->name, $this->journal->name),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
