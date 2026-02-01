<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final readonly class ToggleLLMForJournal
{
    public function __construct(
        private User $user,
        private Journal $journal,
    ) {}

    public function execute(): Journal
    {
        $this->validate();

        $this->journal->has_llm_access = !$this->journal->has_llm_access;
        $this->setAccessKeyIfEnabled();
        $this->journal->save();

        $this->logUserAction();
        $this->updateUserLastActivityDate();

        return $this->journal;
    }

    private function validate(): void
    {
        if ($this->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal not found');
        }

        $rawValue = $this->journal->getAttributes()['has_llm_access'] ?? null;

        if (!in_array($rawValue, [0, 1, true, false], true)) {
            throw ValidationException::withMessages([
                'has_llm_access' => 'LLM visibility must be boolean.',
            ]);
        }
    }

    private function logUserAction(): void
    {
        $state = $this->journal->has_llm_access ? 'enabled' : 'disabled';

        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->journal,
            action: 'journal_llm_visibility_toggled',
            description: sprintf('LLM visibility %s for journal %s', $state, $this->journal->name),
        )->onQueue('low');
    }

    private function setAccessKeyIfEnabled(): void
    {
        if ($this->journal->has_llm_access) {
            $this->journal->llm_access_key = Str::random(64);

            return;
        }

        $this->journal->llm_access_key = null;
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
