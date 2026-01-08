<?php

declare(strict_types=1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleSexualActivity;
use App\View\Presenters\SexualActivityModulePresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SexualActivityModulePresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_sexual_activity_module_data(): void
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

        $presenter = new SexualActivityModulePresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('has_sexual_activity_url', $result);
        $this->assertArrayHasKey('sexual_activity_type_url', $result);
        $this->assertArrayHasKey('sexual_activity_types', $result);
        $this->assertArrayHasKey('reset_url', $result);
        $this->assertArrayHasKey('display_reset', $result);

        $this->assertEquals(
            route('journal.entry.sexual-activity.update', [
                'slug' => $entry->journal->slug,
                'year' => $entry->year,
                'month' => $entry->month,
                'day' => $entry->day,
            ]),
            $result['has_sexual_activity_url'],
        );

        $this->assertEquals(
            route('journal.entry.sexual-activity.type.update', [
                'slug' => $entry->journal->slug,
                'year' => $entry->year,
                'month' => $entry->month,
                'day' => $entry->day,
            ]),
            $result['sexual_activity_type_url'],
        );

        $this->assertEquals(
            route('journal.entry.sexual-activity.reset', [
                'slug' => $entry->journal->slug,
                'year' => $entry->year,
                'month' => $entry->month,
                'day' => $entry->day,
            ]),
            $result['reset_url'],
        );

        $this->assertCount(3, $result['sexual_activity_types']);
        $this->assertEquals('solo', $result['sexual_activity_types'][0]['value']);
        $this->assertEquals('with-partner', $result['sexual_activity_types'][1]['value']);
        $this->assertEquals('intimate-contact', $result['sexual_activity_types'][2]['value']);
        $this->assertFalse($result['sexual_activity_types'][0]['is_selected']);
        $this->assertFalse($result['sexual_activity_types'][1]['is_selected']);
        $this->assertFalse($result['sexual_activity_types'][2]['is_selected']);

        $this->assertFalse($result['display_reset']);
    }

    #[Test]
    public function it_displays_reset_when_had_sexual_activity_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleSexualActivity::factory()->create([
            'journal_entry_id' => $entry->id,
            'had_sexual_activity' => 'yes',
        ]);

        $presenter = new SexualActivityModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
    }

    #[Test]
    public function it_displays_reset_when_sexual_activity_type_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleSexualActivity::factory()->create([
            'journal_entry_id' => $entry->id,
            'sexual_activity_type' => 'with-partner',
        ]);

        $presenter = new SexualActivityModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
        $this->assertFalse($result['sexual_activity_types'][0]['is_selected']);
        $this->assertTrue($result['sexual_activity_types'][1]['is_selected']);
        $this->assertFalse($result['sexual_activity_types'][2]['is_selected']);
    }

    #[Test]
    public function it_does_not_display_reset_when_no_sexual_activity_data_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new SexualActivityModulePresenter($entry);
        $result = $presenter->build();

        $this->assertFalse($result['display_reset']);
    }
}
