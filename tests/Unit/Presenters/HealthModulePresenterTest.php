<?php

declare(strict_types=1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleHealth;
use App\View\Presenters\HealthModulePresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class HealthModulePresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_health_module_data(): void
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

        $presenter = new HealthModulePresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('health_url', $result);
        $this->assertArrayHasKey('health_options', $result);
        $this->assertArrayHasKey('reset_url', $result);
        $this->assertArrayHasKey('display_reset', $result);
    }

    #[Test]
    public function it_generates_correct_health_url(): void
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

        $presenter = new HealthModulePresenter($entry);
        $result = $presenter->build();

        $this->assertStringContainsString($journal->slug, $result['health_url']);
        $this->assertStringContainsString('2024', $result['health_url']);
        $this->assertStringContainsString('12', $result['health_url']);
        $this->assertStringContainsString('25', $result['health_url']);
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

        $presenter = new HealthModulePresenter($entry);
        $result = $presenter->build();

        $this->assertStringContainsString($journal->slug, $result['reset_url']);
        $this->assertStringContainsString('2024', $result['reset_url']);
        $this->assertStringContainsString('12', $result['reset_url']);
        $this->assertStringContainsString('25', $result['reset_url']);
    }

    #[Test]
    public function it_returns_all_health_options(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleHealth::factory()->create([
            'journal_entry_id' => $entry->id,
            'health' => 'good',
        ]);

        $presenter = new HealthModulePresenter($entry);
        $result = $presenter->build();

        $this->assertCount(3, $result['health_options']);
        $this->assertEquals(__('Not great'), $result['health_options'][0]['label']);
        $this->assertEquals(__('Okay'), $result['health_options'][1]['label']);
        $this->assertEquals(__('Good'), $result['health_options'][2]['label']);
    }

    #[Test]
    public function it_marks_selected_health_option(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleHealth::factory()->create([
            'journal_entry_id' => $entry->id,
            'health' => 'good',
        ]);

        $presenter = new HealthModulePresenter($entry);
        $result = $presenter->build();

        $this->assertFalse($result['health_options'][0]['is_selected']);
        $this->assertFalse($result['health_options'][1]['is_selected']);
        $this->assertTrue($result['health_options'][2]['is_selected']);
    }

    #[Test]
    public function it_displays_reset_when_health_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleHealth::factory()->create([
            'journal_entry_id' => $entry->id,
            'health' => 'okay',
        ]);

        $presenter = new HealthModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
    }

    #[Test]
    public function it_does_not_display_reset_when_no_health_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new HealthModulePresenter($entry);
        $result = $presenter->build();

        $this->assertFalse($result['display_reset']);
    }
}
