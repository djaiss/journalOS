<?php

declare(strict_types=1);

namespace Tests\Unit\Traits;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Traits\PreventPastEntryEdits;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PreventPastEntryEditsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_allows_editing_entries_from_today(): void
    {
        $journal = Journal::factory()->create([
            'can_edit_past' => false,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => now()->year,
            'month' => now()->month,
            'day' => now()->day,
        ]);

        $testClass = new class ($entry) {
            use PreventPastEntryEdits;

            public function __construct(private JournalEntry $entry) {}

            public function test(): void
            {
                $this->preventPastEditsAllowed($this->entry);
            }
        };

        $testClass->test();

        $this->assertTrue(true);
    }

    #[Test]
    public function it_allows_editing_entries_within_seven_days(): void
    {
        $journal = Journal::factory()->create([
            'can_edit_past' => false,
        ]);
        $sixDaysAgo = now()->subDays(6);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => $sixDaysAgo->year,
            'month' => $sixDaysAgo->month,
            'day' => $sixDaysAgo->day,
        ]);

        $testClass = new class ($entry) {
            use PreventPastEntryEdits;

            public function __construct(private JournalEntry $entry) {}

            public function test(): void
            {
                $this->preventPastEditsAllowed($this->entry);
            }
        };

        $testClass->test();

        $this->assertTrue(true);
    }

    #[Test]
    public function it_prevents_editing_entries_older_than_seven_days_when_can_edit_past_is_false(): void
    {
        $journal = Journal::factory()->create([
            'can_edit_past' => false,
        ]);
        $eightDaysAgo = now()->subDays(8);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => $eightDaysAgo->year,
            'month' => $eightDaysAgo->month,
            'day' => $eightDaysAgo->day,
        ]);

        $testClass = new class ($entry) {
            use PreventPastEntryEdits;

            public function __construct(private JournalEntry $entry) {}

            public function test(): void
            {
                $this->preventPastEditsAllowed($this->entry);
            }
        };

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Editing past entries is not allowed for this journal.');

        $testClass->test();
    }

    #[Test]
    public function it_prevents_editing_entries_much_older_than_seven_days(): void
    {
        $journal = Journal::factory()->create([
            'can_edit_past' => false,
        ]);
        $thirtyDaysAgo = now()->subDays(30);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => $thirtyDaysAgo->year,
            'month' => $thirtyDaysAgo->month,
            'day' => $thirtyDaysAgo->day,
        ]);

        $testClass = new class ($entry) {
            use PreventPastEntryEdits;

            public function __construct(private JournalEntry $entry) {}

            public function test(): void
            {
                $this->preventPastEditsAllowed($this->entry);
            }
        };

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Editing past entries is not allowed for this journal.');

        $testClass->test();
    }

    #[Test]
    public function it_allows_editing_old_entries_when_can_edit_past_is_true(): void
    {
        $journal = Journal::factory()->create([
            'can_edit_past' => true,
        ]);
        $thirtyDaysAgo = now()->subDays(30);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => $thirtyDaysAgo->year,
            'month' => $thirtyDaysAgo->month,
            'day' => $thirtyDaysAgo->day,
        ]);

        $testClass = new class ($entry) {
            use PreventPastEntryEdits;

            public function __construct(private JournalEntry $entry) {}

            public function test(): void
            {
                $this->preventPastEditsAllowed($this->entry);
            }
        };

        $testClass->test();

        $this->assertTrue(true);
    }

    #[Test]
    public function it_allows_editing_very_old_entries_when_can_edit_past_is_true(): void
    {
        $journal = Journal::factory()->create([
            'can_edit_past' => true,
        ]);
        $oneYearAgo = now()->subDays(365);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => $oneYearAgo->year,
            'month' => $oneYearAgo->month,
            'day' => $oneYearAgo->day,
        ]);

        $testClass = new class ($entry) {
            use PreventPastEntryEdits;

            public function __construct(private JournalEntry $entry) {}

            public function test(): void
            {
                $this->preventPastEditsAllowed($this->entry);
            }
        };

        $testClass->test();

        $this->assertTrue(true);
    }

    #[Test]
    public function it_allows_editing_entries_exactly_seven_days_old(): void
    {
        $journal = Journal::factory()->create([
            'can_edit_past' => false,
        ]);
        $sevenDaysAgo = now()->subDays(7);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => $sevenDaysAgo->year,
            'month' => $sevenDaysAgo->month,
            'day' => $sevenDaysAgo->day,
        ]);

        $testClass = new class ($entry) {
            use PreventPastEntryEdits;

            public function __construct(private JournalEntry $entry) {}

            public function test(): void
            {
                $this->preventPastEditsAllowed($this->entry);
            }
        };

        $testClass->test();

        $this->assertTrue(true);
    }
}
