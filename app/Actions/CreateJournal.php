<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class CreateJournal
{
    private Journal $journal;

    public function __construct(
        private readonly User $user,
        private readonly string $name,
    ) {}

    public function execute(): Journal
    {
        $this->validate();
        $this->create();
        $this->generateSlug();
        $this->log();

        return $this->journal;
    }

    private function validate(): void
    {
        // make sure the journal name doesn't contain any special characters
        if (in_array(preg_match('/^[a-zA-Z0-9\s\-_]+$/', $this->name), [0, false], true)) {
            throw ValidationException::withMessages([
                'journal_name' => 'Journal name can only contain letters, numbers, spaces, hyphens and underscores',
            ]);
        }
    }

    private function create(): void
    {
        $this->journal = Journal::create([
            'user_id' => $this->user->id,
            'name' => $this->name,
        ]);
    }

    private function generateSlug(): void
    {
        $slug = $this->journal->id . '-' . Str::of($this->name)->slug('-');

        $this->journal->slug = $slug;
        $this->journal->save();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->journal,
            action: 'journal_creation',
            description: sprintf('Created a journal called %s', $this->name),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
