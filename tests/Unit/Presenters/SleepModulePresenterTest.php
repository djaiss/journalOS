<?php

declare(strict_types=1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\View\Presenters\SleepModulePresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class SleepModulePresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_sleep_module_data_with_default_times(): void
    {
        $journal = Journal::factory()->create([
            'slug' => 'my-journal',
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
            'bedtime' => null,
            'wake_up_time' => null,
        ]);

        $presenter = new SleepModulePresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('bedtime', $result);
        $this->assertArrayHasKey('wake_up_time', $result);
        $this->assertArrayHasKey('previous_bedtime_url', $result);
        $this->assertArrayHasKey('next_bedtime_url', $result);
        $this->assertArrayHasKey('previous_wake_up_url', $result);
        $this->assertArrayHasKey('next_wake_up_url', $result);
        $this->assertArrayHasKey('bedtime_update_url', $result);
        $this->assertArrayHasKey('wake_up_time_update_url', $result);

        $this->assertCount(5, $result['bedtime']);
        $this->assertCount(5, $result['wake_up_time']);
    }

    #[Test]
    public function it_builds_with_existing_bedtime_and_wake_up_time(): void
    {
        $journal = Journal::factory()->create([
            'slug' => 'my-journal',
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
            'bedtime' => '22:00',
            'wake_up_time' => '06:00',
        ]);

        $presenter = new SleepModulePresenter($entry);
        $result = $presenter->build();

        $bedtimeRange = $result['bedtime'];
        $wakeUpRange = $result['wake_up_time'];

        $this->assertTrue($bedtimeRange->contains('time', '22:00'));
        $this->assertTrue($wakeUpRange->contains('time', '06:00'));

        $selectedBedtime = $bedtimeRange->firstWhere('is_selected', true);
        $this->assertEquals('22:00', $selectedBedtime['time']);

        $selectedWakeUp = $wakeUpRange->firstWhere('is_selected', true);
        $this->assertEquals('06:00', $selectedWakeUp['time']);
    }

    #[Test]
    public function it_shifts_bedtime_range_when_not_skipping(): void
    {
        $journal = Journal::factory()->create([
            'slug' => 'my-journal',
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
            'bedtime' => '22:00',
            'wake_up_time' => '06:00',
        ]);

        $presenter = new SleepModulePresenter($entry);
        $result = $presenter->build();

        $bedtimeRange = $result['bedtime'];
        $this->assertEquals('20:00', $bedtimeRange[0]['time']);
    }

    #[Test]
    public function it_does_not_shift_when_skip_shift_is_true(): void
    {
        $journal = Journal::factory()->create([
            'slug' => 'my-journal',
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
            'bedtime' => '22:00',
            'wake_up_time' => '06:00',
        ]);

        $presenter = new SleepModulePresenter($entry);
        $result = $presenter->build('22:00', '06:00', skipShift: true);

        $bedtimeRange = $result['bedtime'];
        $this->assertEquals('22:00', $bedtimeRange[0]['time']);
    }

    #[Test]
    public function it_generates_correct_navigation_urls(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
            'bedtime' => null,
            'wake_up_time' => null,
        ]);

        $presenter = new SleepModulePresenter($entry);
        $result = $presenter->build();

        $this->assertStringContainsString($journal->slug, $result['previous_bedtime_url']);
        $this->assertStringContainsString('2024', $result['previous_bedtime_url']);
        $this->assertStringContainsString('12', $result['previous_bedtime_url']);
        $this->assertStringContainsString('25', $result['previous_bedtime_url']);

        $this->assertStringContainsString('/15:00/', $result['previous_bedtime_url']);
        $this->assertStringContainsString('/01:00/', $result['next_bedtime_url']);

        $this->assertStringContainsString('/01:00', $result['previous_wake_up_url']);
        $this->assertStringContainsString('/11:00', $result['next_wake_up_url']);
    }

    #[Test]
    public function it_generates_correct_update_urls(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
        ]);

        $presenter = new SleepModulePresenter($entry);
        $result = $presenter->build();

        $expectedBedtimeUrl = route('journal.entry.sleep.bedtime.update', [
            'slug' => $journal->slug,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
        ]);

        $expectedWakeUpUrl = route('journal.entry.sleep.wake_up_time.update', [
            'slug' => $journal->slug,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
        ]);

        $this->assertEquals($expectedBedtimeUrl, $result['bedtime_update_url']);
        $this->assertEquals($expectedWakeUpUrl, $result['wake_up_time_update_url']);
    }

    #[Test]
    public function it_uses_custom_default_times(): void
    {
        $journal = Journal::factory()->create([
            'slug' => 'my-journal',
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
            'bedtime' => null,
            'wake_up_time' => null,
        ]);

        $presenter = new SleepModulePresenter($entry);
        $result = $presenter->build('23:00', '07:00', skipShift: true);

        $bedtimeRange = $result['bedtime'];
        $wakeUpRange = $result['wake_up_time'];

        $this->assertEquals('23:00', $bedtimeRange[0]['time']);
        $this->assertEquals('07:00', $wakeUpRange[0]['time']);
    }

    #[Test]
    public function it_handles_invalid_time_format_gracefully(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
            'bedtime' => 'invalid',
            'wake_up_time' => null,
        ]);

        $presenter = new SleepModulePresenter($entry);
        $result = $presenter->build(skipShift: true);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('bedtime', $result);
    }

    #[Test]
    public function it_handles_empty_string_times(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
            'bedtime' => '',
            'wake_up_time' => '  ',
        ]);

        $presenter = new SleepModulePresenter($entry);
        $result = $presenter->build(skipShift: true);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('bedtime', $result);
        $this->assertArrayHasKey('wake_up_time', $result);
    }
}
