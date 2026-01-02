<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\ResetTravelData;
use App\Jobs\CheckPresenceOfContentInJournalEntry;
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

final class ResetTravelDataTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resets_travel_data_for_a_journal_entry(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'has_traveled_today' => 'yes',
            'travel_mode' => ['car', 'train'],
        ]);

        $result = (new ResetTravelData(
            user: $user,
            entry: $entry,
        ))->execute();

        $this->assertNull($result->has_traveled_today);
        $this->assertNull($result->travel_mode);

        $this->assertDatabaseHas('journal_entries', [
            'id' => $entry->id,
            'has_traveled_today' => null,
            'travel_mode' => null,
        ]);

        $this->assertInstanceOf(JournalEntry::class, $result);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'travel_data_reset' && $job->user->id === $user->id;
            },
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: function (UpdateUserLastActivityDate $job) use ($user): bool {
                return $job->user->id === $user->id;
            },
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: CheckPresenceOfContentInJournalEntry::class,
            callback: function (CheckPresenceOfContentInJournalEntry $job) use ($entry): bool {
                return $job->entry->id === $entry->id;
            },
        );
    }

    #[Test]
    public function it_throws_when_entry_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Journal not found');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->for($otherUser)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        (new ResetTravelData(
            user: $user,
            entry: $entry,
        ))->execute();
    }
}
