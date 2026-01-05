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

final readonly class RenameJournal
{
    public function __construct(
        private User $user,
        private Journal $journal,
        private string $name,
    ) {}

    public function execute(): Journal
    {
        $sanitizedName = TextSanitizer::plainText($this->name);

        $this->validate($sanitizedName);
        $this->rename($sanitizedName);
        $this->updateUserLastActivityDate();
        $this->log($sanitizedName);

        return $this->journal;
    }

    private function validate(string $sanitizedName): void
    {
        if ($this->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }

        if ($sanitizedName === '') {
            throw ValidationException::withMessages([
                'journal_name' => 'Journal name can only contain letters, numbers, spaces, hyphens and underscores',
            ]);
        }

        if (in_array(preg_match('/^[a-zA-Z0-9\s\-_]+$/', $sanitizedName), [0, false], true)) {
            throw ValidationException::withMessages([
                'journal_name' => 'Journal name can only contain letters, numbers, spaces, hyphens and underscores',
            ]);
        }
    }

    private function rename(string $sanitizedName): void
    {
        $this->journal->update([
            'name' => $sanitizedName,
            'slug' => $this->journal->id . '-' . Str::of($sanitizedName)->slug('-'),
        ]);
    }

    private function log(string $sanitizedName): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->journal,
            action: 'journal_rename',
            description: sprintf('Renamed the journal to %s', $sanitizedName),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
