<?php

declare(strict_types=1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleDayType;
use App\View\Presenters\DayTypeModulePresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class DayTypeModulePresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_day_type_module_data(): void
    {
        $journal = Journal::factory()->create([
            'slug' => 'my-journal',
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
        ]);

        $presenter = new DayTypeModulePresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('day_type_url', $result);
        $this->assertArrayHasKey('day_types', $result);
        $this->assertArrayHasKey('reset_url', $result);
        $this->assertArrayHasKey('display_reset', $result);
    }

    #[Test]
    public function it_generates_correct_day_type_url(): void
    {
        $journal = Journal::factory()->create([
            'slug' => 'my-journal',
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
        ]);

        $presenter = new DayTypeModulePresenter($entry);
        $result = $presenter->build();

        $this->assertStringContainsString($journal->slug, $result['day_type_url']);
        $this->assertStringContainsString('2024', $result['day_type_url']);
        $this->assertStringContainsString('12', $result['day_type_url']);
        $this->assertStringContainsString('25', $result['day_type_url']);
    }

    #[Test]
    public function it_generates_correct_reset_url(): void
    {
        $journal = Journal::factory()->create([
            'slug' => 'my-journal',
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
        ]);

        $presenter = new DayTypeModulePresenter($entry);
        $result = $presenter->build();

        $this->assertStringContainsString($journal->slug, $result['reset_url']);
        $this->assertStringContainsString('2024', $result['reset_url']);
        $this->assertStringContainsString('12', $result['reset_url']);
        $this->assertStringContainsString('25', $result['reset_url']);
    }

    #[Test]
    public function it_returns_all_day_types(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new DayTypeModulePresenter($entry);
        $result = $presenter->build();

        $this->assertCount(count(ModuleDayType::DAY_TYPES), $result['day_types']);
        $this->assertEquals(__('Workday'), $result['day_types'][0]['label']);
        $this->assertEquals(__('Day off'), $result['day_types'][1]['label']);
        $this->assertEquals(__('Weekend'), $result['day_types'][2]['label']);
        $this->assertEquals(__('Vacation'), $result['day_types'][3]['label']);
        $this->assertEquals(__('Sick day'), $result['day_types'][4]['label']);
    }

    #[Test]
    public function it_marks_selected_day_type(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleDayType::factory()->create([
            'journal_entry_id' => $entry->id,
            'day_type' => 'workday',
        ]);

        $presenter = new DayTypeModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['day_types'][0]['is_selected']);
        $this->assertFalse($result['day_types'][1]['is_selected']);
        $this->assertFalse($result['day_types'][2]['is_selected']);
        $this->assertFalse($result['day_types'][3]['is_selected']);
        $this->assertFalse($result['day_types'][4]['is_selected']);
    }

    #[Test]
    public function it_displays_reset_when_day_type_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleDayType::factory()->create([
            'journal_entry_id' => $entry->id,
            'day_type' => 'vacation',
        ]);

        $presenter = new DayTypeModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
    }

    #[Test]
    public function it_does_not_display_reset_when_no_day_type_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new DayTypeModulePresenter($entry);
        $result = $presenter->build();

        $this->assertFalse($result['display_reset']);
    }
}
