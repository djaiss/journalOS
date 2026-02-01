<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\RenameBook;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RenameBookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_renames_a_book(): void
    {
        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
            'name' => 'The Great Gatsby',
        ]);

        $updatedBook = new RenameBook(
            user: $user,
            book: $book,
            name: 'The Greatest Gatsby',
        )->execute();

        $this->assertEquals('The Greatest Gatsby', $updatedBook->name);
        $this->assertInstanceOf(Book::class, $updatedBook);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job) => $job->action === 'book_rename' && $job->user->id === $user->id,
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: fn (UpdateUserLastActivityDate $job) => $job->user->id === $user->id,
        );
    }

    #[Test]
    public function it_throws_an_exception_if_book_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Book not found');

        $user = User::factory()->create();
        $otherBook = Book::factory()->create();

        new RenameBook(
            user: $user,
            book: $otherBook,
            name: 'New Name',
        )->execute();
    }
}
