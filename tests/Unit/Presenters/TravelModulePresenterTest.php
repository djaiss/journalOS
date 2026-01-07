<?php

declare(strict_types=1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleTravel;
use App\View\Presenters\TravelModulePresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class TravelModulePresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_travel_module_data(): void
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

        $presenter = new TravelModulePresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('has_traveled_today', $result);
        $this->assertArrayHasKey('has_traveled_url', $result);
        $this->assertArrayHasKey('travel_mode', $result);
        $this->assertArrayHasKey('travel_mode_url', $result);
        $this->assertArrayHasKey('travel_modes', $result);
        $this->assertArrayHasKey('reset_url', $result);
        $this->assertArrayHasKey('display_reset', $result);

        $this->assertEquals(
            route('journal.entry.travel.update', [
                'slug' => $entry->journal->slug,
                'year' => $entry->year,
                'month' => $entry->month,
                'day' => $entry->day,
            ]),
            $result['has_traveled_url'],
        );

        $this->assertEquals(
            route('journal.entry.travel.mode.update', [
                'slug' => $entry->journal->slug,
                'year' => $entry->year,
                'month' => $entry->month,
                'day' => $entry->day,
            ]),
            $result['travel_mode_url'],
        );

        $this->assertEquals(
            route('journal.entry.travel.reset', [
                'slug' => $entry->journal->slug,
                'year' => $entry->year,
                'month' => $entry->month,
                'day' => $entry->day,
            ]),
            $result['reset_url'],
        );

        $this->assertCount(8, $result['travel_modes']);
        $this->assertEquals('car', $result['travel_modes'][0]['value']);
        $this->assertEquals('plane', $result['travel_modes'][1]['value']);
        $this->assertEquals('train', $result['travel_modes'][2]['value']);
        $this->assertEquals('bike', $result['travel_modes'][3]['value']);
        $this->assertEquals('bus', $result['travel_modes'][4]['value']);
        $this->assertEquals('walk', $result['travel_modes'][5]['value']);
        $this->assertEquals('boat', $result['travel_modes'][6]['value']);
        $this->assertEquals('other', $result['travel_modes'][7]['value']);
        $this->assertFalse($result['travel_modes'][0]['is_selected']);
        $this->assertFalse($result['travel_modes'][1]['is_selected']);
        $this->assertFalse($result['travel_modes'][2]['is_selected']);
        $this->assertFalse($result['travel_modes'][3]['is_selected']);
        $this->assertFalse($result['travel_modes'][4]['is_selected']);
        $this->assertFalse($result['travel_modes'][5]['is_selected']);
        $this->assertFalse($result['travel_modes'][6]['is_selected']);
        $this->assertFalse($result['travel_modes'][7]['is_selected']);

        $this->assertFalse($result['display_reset']);
    }

    #[Test]
    public function it_marks_selected_travel_mode(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleTravel::factory()->create([
            'journal_entry_id' => $entry->id,
            'travel_mode' => ['plane'],
        ]);

        $presenter = new TravelModulePresenter($entry);
        $result = $presenter->build();

        $selectedModes = $result['travel_modes']->filter(fn($mode) => $mode['is_selected']);
        $this->assertCount(1, $selectedModes);

        $selectedMode = $selectedModes->first();
        $this->assertEquals('plane', $selectedMode['value']);
        $this->assertEquals(__('Plane'), $selectedMode['label']);
    }

    #[Test]
    public function it_marks_multiple_selected_travel_modes(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleTravel::factory()->create([
            'journal_entry_id' => $entry->id,
            'travel_mode' => ['car', 'plane', 'train'],
        ]);

        $presenter = new TravelModulePresenter($entry);
        $result = $presenter->build();

        $selectedModes = $result['travel_modes']->filter(fn($mode) => $mode['is_selected']);
        $this->assertCount(3, $selectedModes);

        $selectedValues = $selectedModes->pluck('value')->toArray();
        $this->assertContains('car', $selectedValues);
        $this->assertContains('plane', $selectedValues);
        $this->assertContains('train', $selectedValues);
    }

    #[Test]
    public function it_translates_travel_mode_labels(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new TravelModulePresenter($entry);
        $result = $presenter->build();

        $this->assertEquals(__('Car'), $result['travel_modes'][0]['label']);
        $this->assertEquals(__('Plane'), $result['travel_modes'][1]['label']);
        $this->assertEquals(__('Train'), $result['travel_modes'][2]['label']);
        $this->assertEquals(__('Bike'), $result['travel_modes'][3]['label']);
        $this->assertEquals(__('Bus'), $result['travel_modes'][4]['label']);
        $this->assertEquals(__('Walk'), $result['travel_modes'][5]['label']);
        $this->assertEquals(__('Boat'), $result['travel_modes'][6]['label']);
        $this->assertEquals(__('Other'), $result['travel_modes'][7]['label']);
    }

    #[Test]
    public function it_displays_reset_when_has_traveled_today_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleTravel::factory()->create([
            'journal_entry_id' => $entry->id,
            'has_traveled_today' => 'yes',
        ]);

        $presenter = new TravelModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
    }

    #[Test]
    public function it_displays_reset_when_travel_mode_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleTravel::factory()->create([
            'journal_entry_id' => $entry->id,
            'travel_mode' => ['plane'],
        ]);

        $presenter = new TravelModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
    }

    #[Test]
    public function it_does_not_display_reset_when_no_travel_data_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new TravelModulePresenter($entry);
        $result = $presenter->build();

        $this->assertFalse($result['display_reset']);
    }
}
