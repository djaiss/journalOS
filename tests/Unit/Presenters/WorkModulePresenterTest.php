<?php

declare(strict_types=1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleWork;
use App\View\Presenters\WorkModulePresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class WorkModulePresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_work_module_data(): void
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

        $presenter = new WorkModulePresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('work_url', $result);
        $this->assertArrayHasKey('work_modes', $result);
        $this->assertArrayHasKey('work_loads', $result);
        $this->assertArrayHasKey('reset_url', $result);
        $this->assertArrayHasKey('display_reset', $result);

        $this->assertEquals(
            route('journal.entry.work.update', [
                'slug' => $entry->journal->slug,
                'year' => $entry->year,
                'month' => $entry->month,
                'day' => $entry->day,
            ]),
            $result['work_url'],
        );

        $this->assertEquals(
            route('journal.entry.work.reset', [
                'slug' => $entry->journal->slug,
                'year' => $entry->year,
                'month' => $entry->month,
                'day' => $entry->day,
            ]),
            $result['reset_url'],
        );

        $this->assertCount(3, $result['work_modes']);
        $this->assertEquals('remote', $result['work_modes'][0]['value']);
        $this->assertEquals('on-site', $result['work_modes'][1]['value']);
        $this->assertEquals('hybrid', $result['work_modes'][2]['value']);
        $this->assertFalse($result['work_modes'][0]['is_selected']);
        $this->assertFalse($result['work_modes'][1]['is_selected']);
        $this->assertFalse($result['work_modes'][2]['is_selected']);

        $this->assertCount(3, $result['work_loads']);
        $this->assertEquals('light', $result['work_loads'][0]['value']);
        $this->assertEquals('medium', $result['work_loads'][1]['value']);
        $this->assertEquals('heavy', $result['work_loads'][2]['value']);
        $this->assertFalse($result['work_loads'][0]['is_selected']);
        $this->assertFalse($result['work_loads'][1]['is_selected']);
        $this->assertFalse($result['work_loads'][2]['is_selected']);

        $this->assertFalse($result['display_reset']);
    }

    #[Test]
    public function it_displays_reset_when_worked_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleWork::factory()->create([
            'journal_entry_id' => $entry->id,
            'worked' => 'yes',
        ]);

        $presenter = new WorkModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
    }

    #[Test]
    public function it_displays_reset_when_work_mode_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleWork::factory()->create([
            'journal_entry_id' => $entry->id,
            'work_mode' => 'remote',
        ]);

        $presenter = new WorkModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
        $this->assertTrue($result['work_modes'][0]['is_selected']);
        $this->assertFalse($result['work_modes'][1]['is_selected']);
        $this->assertFalse($result['work_modes'][2]['is_selected']);
    }

    #[Test]
    public function it_displays_reset_when_work_load_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleWork::factory()->create([
            'journal_entry_id' => $entry->id,
            'work_load' => 'heavy',
        ]);

        $presenter = new WorkModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
    }

    #[Test]
    public function it_displays_reset_when_work_procrastinated_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleWork::factory()->create([
            'journal_entry_id' => $entry->id,
            'work_procrastinated' => 'yes',
        ]);

        $presenter = new WorkModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
    }

    #[Test]
    public function it_does_not_display_reset_when_no_work_data_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new WorkModulePresenter($entry);
        $result = $presenter->build();

        $this->assertFalse($result['display_reset']);
    }
}
