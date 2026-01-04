<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\ResetBookData;
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

final class ResetBookDataTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resets_book_data_for_a_journal_entry(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();
        $book1 = Book::factory()->for($user)->create();
        $book2 = Book::factory()->for($user)->create();

        DB::table('book_journal_entry')->insert([
            [
                'book_id' => $book1->id,
                'journal_entry_id' => $entry->id,
                'status' => BookStatus::STARTED->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'book_id' => $book2->id,
                'journal_entry_id' => $entry->id,
                'status' => BookStatus::FINISHED->value,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        $this->assertDatabaseHas('book_journal_entry', [
            'book_id' => $book1->id,
            'journal_entry_id' => $entry->id,
        ]);

        $this->assertDatabaseHas('book_journal_entry', [
            'book_id' => $book2->id,
            'journal_entry_id' => $entry->id,
        ]);

        $result = (new ResetBookData(
            user: $user,
            entry: $entry,
        ))->execute();

        $this->assertDatabaseMissing('book_journal_entry', [
            'journal_entry_id' => $entry->id,
        ]);

        $this->assertInstanceOf(JournalEntry::class, $result);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'book_data_reset' && $job->user->id === $user->id;
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
    public function it_throws_when_entry_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Journal not found');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->for($otherUser)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        (new ResetBookData(
            user: $user,
            entry: $entry,
        ))->execute();
    }
}
