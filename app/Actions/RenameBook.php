<?php

declare(strict_types = 1);

namespace App\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * This action renames a book.
 */
final readonly class RenameBook
{
    public function __construct(
        private User $user,
        private Book $book,
        private string $name,
    ) {}

    public function execute(): Book
    {
        $this->validate();
        $this->rename();
        $this->updateUserLastActivityDate();
        $this->log();

        return $this->book;
    }

    private function validate(): void
    {
        if ($this->book->user_id !== $this->user->id) {
            throw new ModelNotFoundException('Book not found');
        }
    }

    private function rename(): void
    {
        $this->book->update([
            'name' => $this->name,
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: null,
            action: 'book_rename',
            description: sprintf('Renamed the book to %s', $this->name),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
