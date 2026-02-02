<?php

declare(strict_types = 1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleSocialEvents;
use App\View\Presenters\SocialEventsModulePresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SocialEventsModulePresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_social_events_module_data(): void
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

        $presenter = new SocialEventsModulePresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('social_events_url', $result);
        $this->assertArrayHasKey('event_type_options', $result);
        $this->assertArrayHasKey('tone_options', $result);
        $this->assertArrayHasKey('duration_options', $result);
        $this->assertArrayHasKey('reset_url', $result);
        $this->assertArrayHasKey('display_reset', $result);
    }

    #[Test]
    public function it_generates_correct_social_events_url(): void
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

        $presenter = new SocialEventsModulePresenter($entry);
        $result = $presenter->build();

        $this->assertStringContainsString($journal->slug, $result['social_events_url']);
        $this->assertStringContainsString('2024', $result['social_events_url']);
        $this->assertStringContainsString('12', $result['social_events_url']);
        $this->assertStringContainsString('25', $result['social_events_url']);
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

        $presenter = new SocialEventsModulePresenter($entry);
        $result = $presenter->build();

        $this->assertStringContainsString($journal->slug, $result['reset_url']);
        $this->assertStringContainsString('2024', $result['reset_url']);
        $this->assertStringContainsString('12', $result['reset_url']);
        $this->assertStringContainsString('25', $result['reset_url']);
    }

    #[Test]
    public function it_returns_all_social_event_options(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new SocialEventsModulePresenter($entry);
        $result = $presenter->build();

        $this->assertCount(6, $result['event_type_options']);
        $this->assertCount(3, $result['tone_options']);
        $this->assertCount(3, $result['duration_options']);
        $this->assertEquals(__('Friends'), $result['event_type_options'][0]['label']);
        $this->assertEquals(__('Draining'), $result['tone_options'][2]['label']);
        $this->assertEquals(__('Long'), $result['duration_options'][2]['label']);
    }

    #[Test]
    public function it_marks_selected_social_event_options(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleSocialEvents::factory()->create([
            'journal_entry_id' => $entry->id,
            'event_type' => 'family',
            'tone' => 'neutral',
            'duration' => 'medium',
        ]);

        $presenter = new SocialEventsModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['event_type_options'][1]['is_selected']);
        $this->assertTrue($result['tone_options'][1]['is_selected']);
        $this->assertTrue($result['duration_options'][1]['is_selected']);
    }

    #[Test]
    public function it_displays_reset_when_social_events_are_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleSocialEvents::factory()->create([
            'journal_entry_id' => $entry->id,
            'event_type' => 'networking',
        ]);

        $presenter = new SocialEventsModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
    }

    #[Test]
    public function it_does_not_display_reset_when_no_social_events_are_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new SocialEventsModulePresenter($entry);
        $result = $presenter->build();

        $this->assertFalse($result['display_reset']);
    }
}
