<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\ResetPhysicalActivityData;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ResetPhysicalActivityDataTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resets_physical_activity_data_for_a_journal_entry(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'has_done_physical_activity' => 'yes',
            'activity_type' => 'running',
            'activity_intensity' => 'high',
        ]);

        $result = (new ResetPhysicalActivityData(
            user: $user,
            entry: $entry,
        ))->execute();

        $this->assertNull($result->has_done_physical_activity);
        $this->assertNull($result->activity_type);
        $this->assertNull($result->activity_intensity);

        $this->assertDatabaseHas('journal_entries', [
            'id' => $entry->id,
            'has_done_physical_activity' => null,
            'activity_type' => null,
            'activity_intensity' => null,
        ]);

        $this->assertInstanceOf(JournalEntry::class, $result);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'physical_activity_reset' && $job->user->id === $user->id;
            },
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: function (UpdateUserLastActivityDate $job) use ($user): bool {
                return $job->user->id === $user->id;
            },
        );
    }

    #[Test]
    public function it_throws_when_entry_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Journal entry not found');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        (new ResetPhysicalActivityData(
            user: $user,
            entry: $entry,
        ))->execute();
    }
}
