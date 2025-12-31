<?php

declare(strict_types=1);

namespace Tests\Unit\Helpers;

use App\Helpers\JournalHelper;
use App\Models\Journal;
use App\Models\JournalEntry;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class JournalHelperTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_gets_all_distinct_years_from_journal_entries(): void
    {
        $journal = Journal::factory()->create();

        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2023,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2023,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
        ]);

        $years = JournalHelper::getYears(
            journal: $journal,
            selectedYear: 2023,
        );

        $this->assertCount(3, $years);
        $this->assertEquals((object) [
            'year' => 2024,
            'is_selected' => false,
            'url' => env('APP_URL') . '/journals/' . $journal->slug . '/entries/2024/1/1',
        ], $years[2024]);
        $this->assertEquals((object) [
            'year' => 2023,
            'is_selected' => true,
            'url' => env('APP_URL') . '/journals/' . $journal->slug . '/entries/2023/1/1',
        ], $years[2023]);
        $this->assertEquals((object) [
            'year' => 2022,
            'is_selected' => false,
            'url' => env('APP_URL') . '/journals/' . $journal->slug . '/entries/2022/1/1',
        ], $years[2022]);
    }

    #[Test]
    public function it_returns_empty_collection_when_journal_has_no_entries(): void
    {
        $journal = Journal::factory()->create();

        $years = JournalHelper::getYears(
            journal: $journal,
            selectedYear: 2023,
        );

        $this->assertCount(0, $years);
    }

    #[Test]
    public function it_gets_all_months_in_a_given_year(): void
    {
        Carbon::setTestNow(Carbon::create(2023, 2, 1));
        $journal = Journal::factory()->create();

        $collection = JournalHelper::getMonths(
            journal: $journal,
            year: 2023,
            selectedMonth: 2,
        );

        $this->assertCount(12, $collection);
        $this->assertEquals((object) [
            'month' => 2,
            'month_name' => 'February',
            'entries_count' => 0,
            'is_selected' => true,
            'url' => env('APP_URL') . '/journals/' . $journal->slug . '/entries/2023/2/1',
        ], $collection[2]);
    }
}
