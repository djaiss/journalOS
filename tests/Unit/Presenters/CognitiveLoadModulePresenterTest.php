<?php

declare(strict_types=1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleCognitiveLoad;
use App\View\Presenters\CognitiveLoadModulePresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CognitiveLoadModulePresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_cognitive_load_module_data(): void
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

        $presenter = new CognitiveLoadModulePresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('cognitive_load_url', $result);
        $this->assertArrayHasKey('reset_url', $result);
        $this->assertArrayHasKey('cognitive_load_options', $result);
        $this->assertArrayHasKey('primary_source_options', $result);
        $this->assertArrayHasKey('load_quality_options', $result);
        $this->assertArrayHasKey('display_reset', $result);
        $this->assertArrayHasKey('has_cognitive_load', $result);
    }

    #[Test]
    public function it_generates_correct_cognitive_load_url(): void
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

        $presenter = new CognitiveLoadModulePresenter($entry);
        $result = $presenter->build();

        $this->assertStringContainsString($journal->slug, $result['cognitive_load_url']);
        $this->assertStringContainsString('2024', $result['cognitive_load_url']);
        $this->assertStringContainsString('12', $result['cognitive_load_url']);
        $this->assertStringContainsString('25', $result['cognitive_load_url']);
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

        $presenter = new CognitiveLoadModulePresenter($entry);
        $result = $presenter->build();

        $this->assertStringContainsString($journal->slug, $result['reset_url']);
        $this->assertStringContainsString('2024', $result['reset_url']);
        $this->assertStringContainsString('12', $result['reset_url']);
        $this->assertStringContainsString('25', $result['reset_url']);
    }

    #[Test]
    public function it_returns_all_cognitive_load_options(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new CognitiveLoadModulePresenter($entry);
        $result = $presenter->build();

        $this->assertCount(4, $result['cognitive_load_options']);
        $this->assertEquals(__('Very low'), $result['cognitive_load_options'][0]['label']);
        $this->assertEquals(__('Low'), $result['cognitive_load_options'][1]['label']);
        $this->assertEquals(__('High'), $result['cognitive_load_options'][2]['label']);
        $this->assertEquals(__('Overwhelming'), $result['cognitive_load_options'][3]['label']);
    }

    #[Test]
    public function it_returns_all_primary_source_options(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new CognitiveLoadModulePresenter($entry);
        $result = $presenter->build();

        $this->assertCount(6, $result['primary_source_options']);
        $this->assertEquals(__('Work'), $result['primary_source_options'][0]['label']);
        $this->assertEquals(__('Personal life'), $result['primary_source_options'][1]['label']);
        $this->assertEquals(__('Relationships'), $result['primary_source_options'][2]['label']);
        $this->assertEquals(__('Health'), $result['primary_source_options'][3]['label']);
        $this->assertEquals(__('Uncertainty'), $result['primary_source_options'][4]['label']);
        $this->assertEquals(__('Mixed'), $result['primary_source_options'][5]['label']);
    }

    #[Test]
    public function it_returns_all_load_quality_options(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new CognitiveLoadModulePresenter($entry);
        $result = $presenter->build();

        $this->assertCount(3, $result['load_quality_options']);
        $this->assertEquals(__('Productive'), $result['load_quality_options'][0]['label']);
        $this->assertEquals(__('Mixed'), $result['load_quality_options'][1]['label']);
        $this->assertEquals(__('Mostly wasteful'), $result['load_quality_options'][2]['label']);
    }

    #[Test]
    public function it_marks_selected_options(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleCognitiveLoad::factory()->create([
            'journal_entry_id' => $entry->id,
            'cognitive_load' => 'high',
            'primary_source' => 'health',
            'load_quality' => 'mixed',
        ]);

        $presenter = new CognitiveLoadModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['cognitive_load_options'][2]['is_selected']);
        $this->assertTrue($result['primary_source_options'][3]['is_selected']);
        $this->assertTrue($result['load_quality_options'][1]['is_selected']);
    }

    #[Test]
    public function it_displays_reset_when_data_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleCognitiveLoad::factory()->create([
            'journal_entry_id' => $entry->id,
            'cognitive_load' => 'low',
        ]);

        $presenter = new CognitiveLoadModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
        $this->assertTrue($result['has_cognitive_load']);
    }

    #[Test]
    public function it_does_not_display_reset_when_no_data_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new CognitiveLoadModulePresenter($entry);
        $result = $presenter->build();

        $this->assertFalse($result['display_reset']);
        $this->assertFalse($result['has_cognitive_load']);
    }
}
