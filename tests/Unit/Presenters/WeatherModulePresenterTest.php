<?php

declare(strict_types=1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleWeather;
use App\View\Presenters\WeatherModulePresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class WeatherModulePresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_weather_module_data(): void
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

        $presenter = new WeatherModulePresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('weather_url', $result);
        $this->assertArrayHasKey('reset_url', $result);
        $this->assertArrayHasKey('condition_options', $result);
        $this->assertArrayHasKey('temperature_range_options', $result);
        $this->assertArrayHasKey('precipitation_options', $result);
        $this->assertArrayHasKey('daylight_options', $result);
        $this->assertArrayHasKey('display_reset', $result);
    }

    #[Test]
    public function it_generates_correct_weather_url(): void
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

        $presenter = new WeatherModulePresenter($entry);
        $result = $presenter->build();

        $this->assertStringContainsString($journal->slug, $result['weather_url']);
        $this->assertStringContainsString('2024', $result['weather_url']);
        $this->assertStringContainsString('12', $result['weather_url']);
        $this->assertStringContainsString('25', $result['weather_url']);
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

        $presenter = new WeatherModulePresenter($entry);
        $result = $presenter->build();

        $this->assertStringContainsString($journal->slug, $result['reset_url']);
        $this->assertStringContainsString('2024', $result['reset_url']);
        $this->assertStringContainsString('12', $result['reset_url']);
        $this->assertStringContainsString('25', $result['reset_url']);
    }

    #[Test]
    public function it_returns_all_weather_options(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleWeather::factory()->create([
            'journal_entry_id' => $entry->id,
            'condition' => 'sunny',
            'temperature_range' => 'warm',
            'precipitation' => 'none',
            'daylight' => 'normal',
        ]);

        $presenter = new WeatherModulePresenter($entry);
        $result = $presenter->build();

        $this->assertCount(5, $result['condition_options']);
        $this->assertCount(5, $result['temperature_range_options']);
        $this->assertCount(3, $result['precipitation_options']);
        $this->assertCount(3, $result['daylight_options']);
        $this->assertEquals(__('Sunny'), $result['condition_options'][0]['label']);
        $this->assertEquals(__('Very cold'), $result['temperature_range_options'][0]['label']);
        $this->assertEquals(__('None'), $result['precipitation_options'][0]['label']);
        $this->assertEquals(__('Very short'), $result['daylight_options'][0]['label']);
    }

    #[Test]
    public function it_marks_selected_weather_options(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleWeather::factory()->create([
            'journal_entry_id' => $entry->id,
            'condition' => 'cloudy',
            'temperature_range' => 'mild',
            'precipitation' => 'light',
            'daylight' => 'very_long',
        ]);

        $presenter = new WeatherModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['condition_options'][1]['is_selected']);
        $this->assertTrue($result['temperature_range_options'][2]['is_selected']);
        $this->assertTrue($result['precipitation_options'][1]['is_selected']);
        $this->assertTrue($result['daylight_options'][2]['is_selected']);
    }

    #[Test]
    public function it_displays_reset_when_weather_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleWeather::factory()->create([
            'journal_entry_id' => $entry->id,
            'condition' => 'snow',
        ]);

        $presenter = new WeatherModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
    }

    #[Test]
    public function it_does_not_display_reset_when_no_weather_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new WeatherModulePresenter($entry);
        $result = $presenter->build();

        $this->assertFalse($result['display_reset']);
    }
}
