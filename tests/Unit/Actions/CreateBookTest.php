<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\CreateBook;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Book;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CreateBookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_creates_a_book(): void
    {
        $user = User::factory()->create();

        $book = new CreateBook(
            user: $user,
            name: 'The Great Gatsby',
        )->execute();

        $this->assertEquals('The Great Gatsby', $book->name);
        $this->assertNull($book->progress);
        $this->assertEquals($user->id, $book->user_id);
        $this->assertInstanceOf(Book::class, $book);

        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'user_id' => $user->id,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'book_creation' && $job->user->id === $user->id;
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
    public function it_allows_duplicate_book_names_for_same_user(): void
    {
        $user = User::factory()->create();

        $book1 = new CreateBook(
            user: $user,
            name: 'Harry Potter',
        )->execute();

        $book2 = new CreateBook(
            user: $user,
            name: 'Harry Potter',
        )->execute();

        $this->assertNotEquals($book1->id, $book2->id);
        $this->assertEquals($book1->name, $book2->name);
        $this->assertEquals(2, Book::query()->where('user_id', $user->id)->count());
    }

    #[Test]
    public function it_allows_same_book_name_for_different_users(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        $book1 = new CreateBook(
            user: $user1,
            name: 'To Kill a Mockingbird',
        )->execute();

        $book2 = new CreateBook(
            user: $user2,
            name: 'To Kill a Mockingbird',
        )->execute();

        $this->assertNotEquals($book1->id, $book2->id);
        $this->assertEquals($book1->name, $book2->name);
        $this->assertEquals($user1->id, $book1->user_id);
        $this->assertEquals($user2->id, $book2->user_id);
    }
}
