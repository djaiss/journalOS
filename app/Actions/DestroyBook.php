<?php

declare(strict_types=1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * This action deletes a book.
 */
final readonly class DestroyBook
{
    public function __construct(
        private User $user,
        private Book $book,
    ) {}

    public function execute(): void
    {
        $this->validate();
        $this->delete();
        $this->log();
        $this->updateUserLastActivityDate();
    }

    private function validate(): void
    {
        if ($this->book->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Book not found');
        }
    }

    private function delete(): void
    {
        $this->book->delete();
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: null,
            action: 'book_deletion',
            description: sprintf('Deleted the book called %s', $this->book->name),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
