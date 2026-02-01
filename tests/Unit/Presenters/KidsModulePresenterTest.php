<?php

declare(strict_types = 1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleKids;
use App\View\Presenters\KidsModulePresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class KidsModulePresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_kids_module_data(): void
    {
        $journal = Journal::factory()->create([
            'slug' => 'family-journal',
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
        ]);

        $presenter = new KidsModulePresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('had_kids_today_url', $result);
        $this->assertArrayHasKey('reset_url', $result);
        $this->assertArrayHasKey('display_reset', $result);

        $this->assertEquals(
            route('journal.entry.kids.update', [
                'slug' => $entry->journal->slug,
                'year' => $entry->year,
                'month' => $entry->month,
                'day' => $entry->day,
            ]),
            $result['had_kids_today_url'],
        );

        $this->assertEquals(
            route('journal.entry.kids.reset', [
                'slug' => $entry->journal->slug,
                'year' => $entry->year,
                'month' => $entry->month,
                'day' => $entry->day,
            ]),
            $result['reset_url'],
        );

        $this->assertFalse($result['display_reset']);
    }

    #[Test]
    public function it_displays_reset_when_kids_today_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleKids::factory()->create([
            'journal_entry_id' => $entry->id,
            'had_kids_today' => 'yes',
        ]);

        $presenter = new KidsModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
    }
}
