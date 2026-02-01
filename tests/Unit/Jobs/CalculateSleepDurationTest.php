<?php

declare(strict_types = 1);

namespace Tests\Unit\Jobs;

use App\Jobs\CalculateSleepDuration;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleSleep;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CalculateSleepDurationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_calculates_sleep_duration(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $moduleSleep = ModuleSleep::factory()->create([
            'journal_entry_id' => $entry->id,
            'bedtime' => '22:00',
            'wake_up_time' => '06:00',
        ]);

        $job = new CalculateSleepDuration($entry);
        $job->handle();

        $moduleSleep->refresh();

        $this->assertEquals('480', $moduleSleep->sleep_duration_in_minutes);
    }

    #[Test]
    public function it_calculates_sleep_duration_with_custom_times(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $moduleSleep = ModuleSleep::factory()->create([
            'journal_entry_id' => $entry->id,
            'bedtime' => '23:30',
            'wake_up_time' => '07:45',
        ]);

        $job = new CalculateSleepDuration($entry);
        $job->handle();

        $moduleSleep->refresh();

        $this->assertEquals('495', $moduleSleep->sleep_duration_in_minutes);
    }

    #[Test]
    public function it_does_not_calculate_when_bedtime_is_null(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $moduleSleep = ModuleSleep::factory()->create([
            'journal_entry_id' => $entry->id,
            'bedtime' => null,
            'wake_up_time' => '06:00',
            'sleep_duration_in_minutes' => null,
        ]);

        $job = new CalculateSleepDuration($entry);
        $job->handle();

        $moduleSleep->refresh();

        $this->assertNull($moduleSleep->sleep_duration_in_minutes);
    }

    #[Test]
    public function it_does_not_calculate_when_wake_up_time_is_null(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $moduleSleep = ModuleSleep::factory()->create([
            'journal_entry_id' => $entry->id,
            'bedtime' => '22:00',
            'wake_up_time' => null,
            'sleep_duration_in_minutes' => null,
        ]);

        $job = new CalculateSleepDuration($entry);
        $job->handle();

        $moduleSleep->refresh();

        $this->assertNull($moduleSleep->sleep_duration_in_minutes);
    }

    #[Test]
    public function it_does_not_calculate_when_both_times_are_null(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $moduleSleep = ModuleSleep::factory()->create([
            'journal_entry_id' => $entry->id,
            'bedtime' => null,
            'wake_up_time' => null,
            'sleep_duration_in_minutes' => null,
        ]);

        $job = new CalculateSleepDuration($entry);
        $job->handle();

        $moduleSleep->refresh();

        $this->assertNull($moduleSleep->sleep_duration_in_minutes);
    }
}
