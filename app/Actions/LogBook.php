<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\BookStatus;
use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Book;
use App\Models\JournalEntry;
use App\Models\User;
use App\Traits\PreventPastEntryEdits;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

/**
 * This action logs a book for the user in a journal entry.
 */
final readonly class LogBook
{
    use PreventPastEntryEdits;

    public function __construct(
        private User $user,
        private JournalEntry $entry,
        private Book $book,
        private BookStatus $status,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->log();
        $this->logUserAction();
        $this->updateUserLastActivityDate();
        $this->refreshContentPresenceStatus();
    }

    private function validate(): void
    {
        if ($this->entry->journal->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Journal entry not found');
        }

        if ($this->book->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Book not found');
        }

        $this->preventPastEditsAllowed($this->entry);
    }

    private function log(): void
    {
        DB::table('book_journal_entry')->insert([
            'book_id' => $this->book->id,
            'journal_entry_id' => $this->entry->id,
            'status' => $this->status->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function logUserAction(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: $this->entry->journal,
            action: 'book_logged',
            description: 'Logged book on ' . $this->entry->getDate(),
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
