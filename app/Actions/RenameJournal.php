<?php

declare(strict_types=1);

namespace App\Actions;

use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class RenameJournal
{
    public function __construct(
        private User $user,
        private Journal $journal,
        private string $name,
    ) {}

    public function execute(): Journal
    {
        $this->validate();
        $this->rename();
        $this->updateUserLastActivityDate();
        $this->log();

        return $this->journal;
    }

    private function validate(): void
    {
        $this->name = TextSanitizer::plainText($this->name);

        if ($this->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }

        if ($this->name === '') {
            throw ValidationException::withMessages([
                'journal_name' => 'Journal name can only contain letters, numbers, spaces, hyphens and underscores',
            ]);
        }

        if (in_array(preg_match('/^[a-zA-Z0-9\s\-_]+$/', $this->name), [0, false], true)) {
            throw ValidationException::withMessages([
                'journal_name' => 'Journal name can only contain letters, numbers, spaces, hyphens and underscores',
            ]);
        }
    }

    private function rename(): void
    {
        $this->journal->update([
            'name' => $this->name,
            'slug' => $this->journal->id . '-' . Str::of($this->name)->slug('-'),
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->journal,
            action: 'journal_rename',
            description: sprintf('Renamed the journal to %s', $this->name),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
