<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CheckPresenceOfContentInJournalEntryTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_sets_has_content_to_false_when_all_fields_are_null(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_content' => true,
            'bedtime' => null,
            'wake_up_time' => null,
            'sleep_duration_in_minutes' => null,
            'worked' => null,
            'work_mode' => null,
            'work_load' => null,
            'work_procrastinated' => null,
            'has_traveled_today' => null,
            'travel_details' => null,
            'travel_mode' => null,
            'day_type' => null,
            'has_done_physical_activity' => null,
            'activity_type' => null,
            'activity_intensity' => null,
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertFalse($entry->has_content);
    }

    #[Test]
    public function it_sets_has_content_to_true_when_bedtime_is_set(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_content' => false,
            'bedtime' => '22:30',
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertTrue($entry->has_content);
    }

    #[Test]
    public function it_sets_has_content_to_true_when_worked_is_set(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_content' => false,
            'worked' => 'yes',
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertTrue($entry->has_content);
    }

    #[Test]
    public function it_sets_has_content_to_true_when_day_type_is_set(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_content' => false,
            'day_type' => 'workday',
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertTrue($entry->has_content);
    }

    #[Test]
    public function it_sets_has_content_to_true_when_physical_activity_is_set(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_content' => false,
            'has_done_physical_activity' => 'yes',
            'activity_type' => 'running',
            'activity_intensity' => 'moderate',
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertTrue($entry->has_content);
    }

    #[Test]
    public function it_sets_has_content_to_true_when_travel_mode_is_set(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create(['user_id' => $user->id]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_content' => false,
            'has_traveled_today' => 'yes',
            'travel_mode' => ['car', 'plane'],
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertTrue($entry->has_content);
    }
}
