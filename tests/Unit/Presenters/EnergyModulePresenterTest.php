<?php

declare(strict_types = 1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleEnergy;
use App\View\Presenters\EnergyModulePresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class EnergyModulePresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_energy_module_data(): void
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

        $presenter = new EnergyModulePresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('energy_url', $result);
        $this->assertArrayHasKey('energy_options', $result);
        $this->assertArrayHasKey('reset_url', $result);
        $this->assertArrayHasKey('display_reset', $result);
    }

    #[Test]
    public function it_generates_correct_energy_url(): void
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

        $presenter = new EnergyModulePresenter($entry);
        $result = $presenter->build();

        $this->assertStringContainsString($journal->slug, $result['energy_url']);
        $this->assertStringContainsString('2024', $result['energy_url']);
        $this->assertStringContainsString('12', $result['energy_url']);
        $this->assertStringContainsString('25', $result['energy_url']);
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

        $presenter = new EnergyModulePresenter($entry);
        $result = $presenter->build();

        $this->assertStringContainsString($journal->slug, $result['reset_url']);
        $this->assertStringContainsString('2024', $result['reset_url']);
        $this->assertStringContainsString('12', $result['reset_url']);
        $this->assertStringContainsString('25', $result['reset_url']);
    }

    #[Test]
    public function it_returns_all_energy_options(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new EnergyModulePresenter($entry);
        $result = $presenter->build();

        $this->assertCount(5, $result['energy_options']);
        $this->assertEquals(__('Very low'), $result['energy_options'][0]['label']);
        $this->assertEquals(__('Low'), $result['energy_options'][1]['label']);
        $this->assertEquals(__('Normal'), $result['energy_options'][2]['label']);
        $this->assertEquals(__('High'), $result['energy_options'][3]['label']);
        $this->assertEquals(__('Very high'), $result['energy_options'][4]['label']);
    }

    #[Test]
    public function it_marks_selected_energy_option(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleEnergy::factory()->create([
            'journal_entry_id' => $entry->id,
            'energy' => 'normal',
        ]);

        $presenter = new EnergyModulePresenter($entry);
        $result = $presenter->build();

        $this->assertFalse($result['energy_options'][0]['is_selected']);
        $this->assertFalse($result['energy_options'][1]['is_selected']);
        $this->assertTrue($result['energy_options'][2]['is_selected']);
        $this->assertFalse($result['energy_options'][3]['is_selected']);
        $this->assertFalse($result['energy_options'][4]['is_selected']);
    }

    #[Test]
    public function it_displays_reset_when_energy_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleEnergy::factory()->create([
            'journal_entry_id' => $entry->id,
            'energy' => 'low',
        ]);

        $presenter = new EnergyModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
    }

    #[Test]
    public function it_does_not_display_reset_when_no_energy_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new EnergyModulePresenter($entry);
        $result = $presenter->build();

        $this->assertFalse($result['display_reset']);
    }
}
