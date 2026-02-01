<?php

declare(strict_types=1);

namespace Tests\Unit\Presenters;

use App\Enums\BookStatus;
use App\Models\Book;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleReading;
use App\Models\User;
use App\View\Presenters\ReadingModulePresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ReadingModulePresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_reading_module_data(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'slug' => 'my-journal',
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
        ]);

        $presenter = new ReadingModulePresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('reading_url', $result);
        $this->assertArrayHasKey('reset_url', $result);
        $this->assertArrayHasKey('books_url', $result);
        $this->assertArrayHasKey('reading_amounts', $result);
        $this->assertArrayHasKey('mental_states', $result);
        $this->assertArrayHasKey('reading_feels', $result);
        $this->assertArrayHasKey('want_continue_options', $result);
        $this->assertArrayHasKey('reading_limits', $result);
    }

    #[Test]
    public function it_generates_correct_urls(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'slug' => 'my-journal',
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
        ]);

        $presenter = new ReadingModulePresenter($entry);
        $result = $presenter->build();

        $this->assertStringContainsString($journal->slug, $result['reading_url']);
        $this->assertStringContainsString('2024', $result['reading_url']);
        $this->assertStringContainsString('12', $result['reading_url']);
        $this->assertStringContainsString('25', $result['reading_url']);

        $this->assertStringContainsString($journal->slug, $result['reset_url']);
        $this->assertStringContainsString('2024', $result['reset_url']);
        $this->assertStringContainsString('12', $result['reset_url']);
        $this->assertStringContainsString('25', $result['reset_url']);
    }

    #[Test]
    public function it_returns_book_suggestions_and_logged_books(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $book = Book::factory()->create([
            'user_id' => $user->id,
            'name' => 'Dune',
        ]);
        $entry->books()->attach($book, ['status' => BookStatus::CONTINUED->value]);

        $presenter = new ReadingModulePresenter($entry);
        $result = $presenter->build();

        $this->assertEquals(['Dune'], $result['book_suggestions']);
        $this->assertCount(1, $result['books']);
        $this->assertEquals('Dune', $result['books'][0]['name']);
    }

    #[Test]
    public function it_marks_selected_reading_amount(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleReading::factory()->create([
            'journal_entry_id' => $entry->id,
            'reading_amount' => 'deep immersion',
        ]);

        $presenter = new ReadingModulePresenter($entry);
        $result = $presenter->build();

        $selected = collect($result['reading_amounts'])
            ->firstWhere('value', 'deep immersion');

        $this->assertTrue($selected['is_selected']);
    }

    #[Test]
    public function it_displays_reset_when_reading_data_exists(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleReading::factory()->create([
            'journal_entry_id' => $entry->id,
            'reading_limit' => 'time',
        ]);

        $presenter = new ReadingModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
    }
}
