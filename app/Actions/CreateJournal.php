<?php

declare(strict_types=1);

namespace App\Actions;

use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class CreateJournal
{
    private Journal $journal;
    private string $sanitizedName = '';

    public function __construct(
        private readonly User $user,
        private readonly string $name,
    ) {}

    public function execute(): Journal
    {
        $this->sanitize();
        $this->validate();
        $this->create();
        $this->generateSlug();
        $this->updateUserLastActivityDate();
        $this->log();

        return $this->journal;
    }

    private function validate(): void
    {
        if ($this->sanitizedName === '') {
            throw ValidationException::withMessages([
                'journal_name' => 'Journal name can only contain letters, numbers, spaces, hyphens and underscores',
            ]);
        }

        // make sure the journal name doesn't contain any special characters
        if (in_array(preg_match('/^[a-zA-Z0-9\s\-_]+$/', $this->sanitizedName), [0, false], true)) {
            throw ValidationException::withMessages([
                'journal_name' => 'Journal name can only contain letters, numbers, spaces, hyphens and underscores',
            ]);
        }
    }

    private function sanitize(): void
    {
        $this->sanitizedName = TextSanitizer::plainText($this->name);
    }

    private function create(): void
    {
        $this->journal = Journal::query()->create([
            'user_id' => $this->user->id,
            'name' => $this->sanitizedName,
        ]);
    }

    private function generateSlug(): void
    {
        $slug = $this->journal->id . '-' . Str::of($this->sanitizedName)->slug('-');

        $this->journal->slug = $slug;
        $this->journal->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->journal,
            action: 'journal_creation',
            description: sprintf('Created a journal called %s', $this->sanitizedName),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
