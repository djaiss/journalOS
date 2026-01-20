<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Modules\Reading;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ReadingBookControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_adds_a_book_to_the_entry(): void
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

        $response = $this->actingAs($user)->post(
            "/journals/{$journal->slug}/entries/2024/6/15/reading/books",
            ['book_name' => 'Dune'],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh()->load('books');
        $this->assertCount(1, $entry->books);
        $this->assertEquals('Dune', $entry->books->first()->name);
    }

    #[Test]
    public function it_removes_a_book_from_the_entry(): void
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

        $response = $this->actingAs($user)->delete(
            "/journals/{$journal->slug}/entries/2024/6/15/reading/books/{$book->id}",
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

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

        $response = $this->actingAs($user)->post(
            "/journals/{$journal->slug}/entries/2024/6/15/reading/books",
            [],
        );

        $response->assertSessionHasErrors('book_name');
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->post(
            "/journals/{$journal->slug}/entries/2024/6/15/reading/books",
            ['book_name' => 'Dune'],
        );

        $response->assertRedirect('/login');
    }
}
