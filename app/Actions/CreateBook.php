<?php

declare(strict_types=1);

namespace App\Actions;

use App\Helpers\TextSanitizer;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Book;
use App\Models\User;

/**
 * This action creates a book for a user.
 *
 * Note: Since book names are encrypted, we cannot efficiently check for duplicates.
 * Users may have multiple books with the same name (e.g., different editions).
 */
final class CreateBook
{
    private Book $book;

    public function __construct(
        private readonly User $user,
        private readonly string $name,
    ) {}

    public function execute(): Book
    {
        $this->create();
        $this->updateUserLastActivityDate();
        $this->log();

        return $this->book;
    }

    private function create(): void
    {
        $this->book = Book::query()->create([
            'user_id' => $this->user->id,
            'name' => TextSanitizer::plainText($this->name),
        ]);
    }

    private function log(): void
    {
        LogUserAction::dispatch(
            user: $this->user,
            journal: null,
            action: 'book_creation',
            description: sprintf('Created a book called %s', $this->name),
        )->onQueue('low');
    }

    private function updateUserLastActivityDate(): void
    {
        UpdateUserLastActivityDate::dispatch($this->user)->onQueue('low');
    }
}
