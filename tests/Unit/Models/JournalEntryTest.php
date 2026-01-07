<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class JournalEntryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_belongs_to_a_journal(): void
    {
        $journal = Journal::factory()->create();
        $journalEntry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $this->assertTrue($journalEntry->journal()->exists());
    }

    #[Test]
    public function it_gets_the_date(): void
    {
        $journalEntry = JournalEntry::factory()->create([
            'day' => 1,
            'month' => 1,
            'year' => 2021,
        ]);

        $this->assertEquals('Friday January 1st, 2021', $journalEntry->getDate());
    }

    #[Test]
    public function it_has_many_books(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $book1 = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $book2 = Book::factory()->create([
            'user_id' => $user->id,
        ]);

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

        $this->assertCount(2, $entry->books);
        $this->assertEquals(BookStatus::STARTED->value, $entry->books->first()->pivot->status);
        $this->assertEquals(BookStatus::FINISHED->value, $entry->books->last()->pivot->status);
    }
}
