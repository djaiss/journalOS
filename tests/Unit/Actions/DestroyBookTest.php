<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\DestroyBook;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Book;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class DestroyBookTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_destroys_a_book(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $book = Book::factory()->create([
            'user_id' => $user->id,
            'name' => 'The Catcher in the Rye',
        ]);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
        ]);

        (new DestroyBook(
            user: $user,
            book: $book,
        ))->execute();

        $this->assertDatabaseMissing('books', [
            'id' => $book->id,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'book_deletion' && $job->user->id === $user->id;
            },
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: function (UpdateUserLastActivityDate $job) use ($user): bool {
                return $job->user->id === $user->id;
            },
        );
    }

    #[Test]
    public function it_throws_an_exception_if_book_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Book not found');

        $user = User::factory()->create();
        $otherBook = Book::factory()->create();

        (new DestroyBook(
            user: $user,
            book: $otherBook,
        ))->execute();
    }
}
