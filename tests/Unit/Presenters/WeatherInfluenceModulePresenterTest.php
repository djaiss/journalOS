<?php

declare(strict_types = 1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleWeatherInfluence;
use App\View\Presenters\WeatherInfluenceModulePresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class WeatherInfluenceModulePresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_weather_influence_module_data(): void
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

        $presenter = new WeatherInfluenceModulePresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('weather_influence_url', $result);
        $this->assertArrayHasKey('reset_url', $result);
        $this->assertArrayHasKey('mood_effect_options', $result);
        $this->assertArrayHasKey('energy_effect_options', $result);
        $this->assertArrayHasKey('plans_influence_options', $result);
        $this->assertArrayHasKey('outside_time_options', $result);
        $this->assertArrayHasKey('display_reset', $result);
    }

    #[Test]
    public function it_generates_correct_weather_influence_url(): void
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

        $presenter = new WeatherInfluenceModulePresenter($entry);
        $result = $presenter->build();

        $this->assertStringContainsString($journal->slug, $result['weather_influence_url']);
        $this->assertStringContainsString('2024', $result['weather_influence_url']);
        $this->assertStringContainsString('12', $result['weather_influence_url']);
        $this->assertStringContainsString('25', $result['weather_influence_url']);
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

        $presenter = new WeatherInfluenceModulePresenter($entry);
        $result = $presenter->build();

        $this->assertStringContainsString($journal->slug, $result['reset_url']);
        $this->assertStringContainsString('2024', $result['reset_url']);
        $this->assertStringContainsString('12', $result['reset_url']);
        $this->assertStringContainsString('25', $result['reset_url']);
    }

    #[Test]
    public function it_returns_all_weather_influence_options(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleWeatherInfluence::factory()->create([
            'journal_entry_id' => $entry->id,
            'mood_effect' => 'positive',
            'energy_effect' => 'boosted',
            'plans_influence' => 'slight',
            'outside_time' => 'some',
        ]);

        $presenter = new WeatherInfluenceModulePresenter($entry);
        $result = $presenter->build();

        $this->assertCount(4, $result['mood_effect_options']);
        $this->assertCount(3, $result['energy_effect_options']);
        $this->assertCount(3, $result['plans_influence_options']);
        $this->assertCount(4, $result['outside_time_options']);
        $this->assertEquals(__('Positive'), $result['mood_effect_options'][0]['label']);
        $this->assertEquals(__('Boosted'), $result['energy_effect_options'][0]['label']);
        $this->assertEquals(__('None'), $result['plans_influence_options'][0]['label']);
        $this->assertEquals(__('A lot'), $result['outside_time_options'][0]['label']);
    }

    #[Test]
    public function it_marks_selected_weather_influence_options(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleWeatherInfluence::factory()->create([
            'journal_entry_id' => $entry->id,
            'mood_effect' => 'negative',
            'energy_effect' => 'drained',
            'plans_influence' => 'significant',
            'outside_time' => 'not_at_all',
        ]);

        $presenter = new WeatherInfluenceModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['mood_effect_options'][3]['is_selected']);
        $this->assertTrue($result['energy_effect_options'][2]['is_selected']);
        $this->assertTrue($result['plans_influence_options'][2]['is_selected']);
        $this->assertTrue($result['outside_time_options'][3]['is_selected']);
    }

    #[Test]
    public function it_displays_reset_when_weather_influence_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleWeatherInfluence::factory()->create([
            'journal_entry_id' => $entry->id,
            'mood_effect' => 'positive',
        ]);

        $presenter = new WeatherInfluenceModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
    }

    #[Test]
    public function it_does_not_display_reset_when_no_weather_influence_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new WeatherInfluenceModulePresenter($entry);
        $result = $presenter->build();

        $this->assertFalse($result['display_reset']);
    }
}
