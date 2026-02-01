<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Journals\Modules\Reading;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ReadingBookControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_adds_a_book_to_the_entry_and_returns_journal_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('POST', "/api/journals/{$journal->id}/2024/6/15/reading/books", [
            'book_name' => 'Dune',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'attributes' => [
                    'modules' => [
                        'reading' => [
                            'books' => [
                                [
                                    'name' => 'Dune',
                                    'status' => BookStatus::CONTINUED->value,
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh()->load('books');
        $this->assertCount(1, $entry->books);
        $this->assertEquals('Dune', $entry->books->first()->name);
    }

    #[Test]
    public function it_removes_a_book_from_the_entry_and_returns_journal_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry->books()->attach($book, ['status' => BookStatus::CONTINUED->value]);

        Sanctum::actingAs($user);

        $response = $this->json('DELETE', "/api/journals/{$journal->id}/2024/6/15/reading/books/{$book->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'attributes' => [
                    'modules' => [
                        'reading' => [
                            'books' => [],
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh()->load('books');
        $this->assertCount(0, $entry->books);
    }

    #[Test]
    public function it_validates_book_name_is_required(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('POST', "/api/journals/{$journal->id}/2024/6/15/reading/books", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('book_name');
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->json('POST', "/api/journals/{$journal->id}/2024/6/15/reading/books", [
            'book_name' => 'Dune',
        ]);

        $response->assertStatus(401);
    }
}
