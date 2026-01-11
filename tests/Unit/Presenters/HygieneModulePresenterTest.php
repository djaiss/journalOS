<?php

declare(strict_types=1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleHygiene;
use App\View\Presenters\HygieneModulePresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class HygieneModulePresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_hygiene_module_data(): void
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

        $presenter = new HygieneModulePresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('hygiene_url', $result);
        $this->assertArrayHasKey('showered_options', $result);
        $this->assertArrayHasKey('brushed_teeth_options', $result);
        $this->assertArrayHasKey('skincare_options', $result);
        $this->assertArrayHasKey('reset_url', $result);
        $this->assertArrayHasKey('display_reset', $result);
    }

    #[Test]
    public function it_generates_correct_hygiene_url(): void
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

        $presenter = new HygieneModulePresenter($entry);
        $result = $presenter->build();

        $this->assertStringContainsString($journal->slug, $result['hygiene_url']);
        $this->assertStringContainsString('2024', $result['hygiene_url']);
        $this->assertStringContainsString('12', $result['hygiene_url']);
        $this->assertStringContainsString('25', $result['hygiene_url']);
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

        $presenter = new HygieneModulePresenter($entry);
        $result = $presenter->build();

        $this->assertStringContainsString($journal->slug, $result['reset_url']);
        $this->assertStringContainsString('2024', $result['reset_url']);
        $this->assertStringContainsString('12', $result['reset_url']);
        $this->assertStringContainsString('25', $result['reset_url']);
    }

    #[Test]
    public function it_returns_all_hygiene_options(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new HygieneModulePresenter($entry);
        $result = $presenter->build();

        $this->assertCount(2, $result['showered_options']);
        $this->assertCount(3, $result['brushed_teeth_options']);
        $this->assertCount(2, $result['skincare_options']);
    }

    #[Test]
    public function it_marks_selected_hygiene_options(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleHygiene::factory()->create([
            'journal_entry_id' => $entry->id,
            'showered' => 'yes',
            'brushed_teeth' => 'pm',
            'skincare' => 'no',
        ]);

        $presenter = new HygieneModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['showered_options'][0]['is_selected']);
        $this->assertTrue($result['brushed_teeth_options'][2]['is_selected']);
        $this->assertTrue($result['skincare_options'][1]['is_selected']);
    }

    #[Test]
    public function it_displays_reset_when_hygiene_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleHygiene::factory()->create([
            'journal_entry_id' => $entry->id,
            'showered' => 'yes',
        ]);

        $presenter = new HygieneModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
    }

    #[Test]
    public function it_does_not_display_reset_when_no_hygiene_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new HygieneModulePresenter($entry);
        $result = $presenter->build();

        $this->assertFalse($result['display_reset']);
    }
}
