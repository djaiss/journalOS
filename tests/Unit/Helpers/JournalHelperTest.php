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

    #[Test]
    public function it_gets_all_days_in_a_given_month(): void
    {
        Carbon::setTestNow(Carbon::create(2023, 2, 15));
        $journal = Journal::factory()->create();

        // Create entries for Feb 2023, only some with content
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2023,
            'month' => 2,
            'day' => 5,
            'has_content' => true,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2023,
            'month' => 2,
            'day' => 10,
            'has_content' => true,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2023,
            'month' => 2,
            'day' => 20,
            'has_content' => false,
        ]);

        $days = JournalHelper::getDaysInMonth(
            journal: $journal,
            year: 2023,
            month: 2,
            day: 15,
        );

        // February 2023 has 28 days
        $this->assertCount(28, $days);

        // Check day 5 has content
        $this->assertEquals((object) [
            'day' => 5,
            'is_today' => false,
            'is_selected' => false,
            'has_content' => true,
            'url' => env('APP_URL') . '/journals/' . $journal->slug . '/entries/2023/2/5',
        ], $days[5]);

        // Check day 10 has content
        $this->assertTrue($days[10]->has_content);

        // Check day 15 is selected and is today but has no content
        $this->assertTrue($days[15]->is_selected);
        $this->assertTrue($days[15]->is_today);
        $this->assertFalse($days[15]->has_content);

        // Check day 20 has no content (has_content is false)
        $this->assertFalse($days[20]->has_content);

        // Check random day without entry has no content
        $this->assertFalse($days[25]->has_content);
    }

    #[Test]
    public function it_correctly_identifies_days_with_content_across_month(): void
    {
        $journal = Journal::factory()->create();

        // Create multiple entries with content
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 3,
            'day' => 1,
            'has_content' => true,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 3,
            'day' => 15,
            'has_content' => true,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 3,
            'day' => 31,
            'has_content' => true,
        ]);

        $days = JournalHelper::getDaysInMonth(
            journal: $journal,
            year: 2024,
            month: 3,
            day: 1,
        );

        // March has 31 days
        $this->assertCount(31, $days);

        // Only days 1, 15, and 31 should have content
        $this->assertTrue($days[1]->has_content);
        $this->assertFalse($days[2]->has_content);
        $this->assertTrue($days[15]->has_content);
        $this->assertFalse($days[16]->has_content);
        $this->assertTrue($days[31]->has_content);
    }

    #[Test]
    public function it_returns_all_days_without_content_when_no_entries_exist(): void
    {
        $journal = Journal::factory()->create();

        $days = JournalHelper::getDaysInMonth(
            journal: $journal,
            year: 2023,
            month: 1,
            day: 1,
        );

        // January has 31 days
        $this->assertCount(31, $days);

        // No day should have content
        foreach ($days as $day) {
            $this->assertFalse($day->has_content);
        }
    }
}
