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
            'worked' => null,
            'work_mode' => null,
            'work_load' => null,
            'work_procrastinated' => null,
            'has_traveled_today' => null,
            'travel_details' => null,
            'travel_mode' => null,
            'had_kids_today' => null,
            'day_type' => null,
            'has_done_physical_activity' => null,
            'activity_type' => null,
            'activity_intensity' => null,
            'had_sexual_activity' => null,
            'sexual_activity_type' => null,
        ]);

        $job = new CheckPresenceOfContentInJournalEntry($entry);
        $job->handle();

        $entry->refresh();
        $this->assertFalse($entry->has_content);
    }
}
