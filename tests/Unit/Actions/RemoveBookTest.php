<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\RemoveBook;
use App\Enums\BookStatus;
use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Book;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RemoveBookTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_removes_book_from_journal_entry(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        DB::table('book_journal_entry')->insert([
            'book_id' => $book->id,
            'journal_entry_id' => $entry->id,
            'status' => BookStatus::STARTED->value,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas('book_journal_entry', [
            'book_id' => $book->id,
            'journal_entry_id' => $entry->id,
        ]);

        (new RemoveBook(
            user: $user,
            entry: $entry,
            book: $book,
        ))->execute();

        $this->assertDatabaseMissing('book_journal_entry', [
            'book_id' => $book->id,
            'journal_entry_id' => $entry->id,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'book_removed' && $job->user->id === $user->id;
            },
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: function (UpdateUserLastActivityDate $job) use ($user): bool {
                return $job->user->id === $user->id;
            },
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: CheckPresenceOfContentInJournalEntry::class,
            callback: function (CheckPresenceOfContentInJournalEntry $job) use ($entry): bool {
                return $job->entry->id === $entry->id;
            },
        );
    }

    #[Test]
    public function it_fails_if_journal_entry_does_not_belong_to_user(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $anotherUser->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Journal entry not found');

        (new RemoveBook(
            user: $user,
            entry: $entry,
            book: $book,
        ))->execute();
    }

    #[Test]
    public function it_fails_if_book_does_not_belong_to_user(): void
    {
        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $book = Book::factory()->create([
            'user_id' => $anotherUser->id,
        ]);

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Book not found');

        (new RemoveBook(
            user: $user,
            entry: $entry,
            book: $book,
        ))->execute();
    }
}
