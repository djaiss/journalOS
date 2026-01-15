<?php

declare(strict_types=1);

namespace App\Actions;

use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Layout;
use App\Models\LayoutModule;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

final class UpdateLayout
{
    public function __construct(
        private readonly User $user,
        private readonly Layout $layout,
        private string $name,
        private readonly int $columnsCount,
    ) {}

    public function execute(): Layout
    {
        $this->validate();
        $this->update();
        $this->updateUserLastActivityDate();
        $this->log();

        return $this->layout;
    }

    private function validate(): void
    {
        $this->name = TextSanitizer::plainText($this->name);

        if (! $this->layout->journal()->where('user_id', $this->user->id)->exists()) {
            throw new ModelNotFoundException('Layout not found');
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

    private function update(): void
    {
        $currentColumns = $this->layout->columns_count;

        DB::transaction(function () use ($currentColumns): void {
            if ($this->columnsCount < $currentColumns) {
                LayoutModule::query()
                    ->where('layout_id', $this->layout->id)
                    ->where('column_number', '>', $this->columnsCount)
                    ->delete();
            }

            $this->layout->update([
                'name' => $this->name,
                'columns_count' => $this->columnsCount,
            ]);
        });
    }

    private function log(): void
    {
        $journal = $this->layout->journal;

        LogUserAction::dispatch(
            user: $this->user,
            journal: $journal,
            action: 'layout_update',
            description: sprintf('Updated the layout %s for the journal %s', $this->name, $journal->name),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
