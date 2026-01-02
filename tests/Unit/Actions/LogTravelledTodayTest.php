<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogTravelledToday;
use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LogTravelledTodayTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_traveled_with_yes(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->for($journal)->create([
            'has_traveled_today' => null,
        ]);

        $result = (new LogTravelledToday(
            user: $user,
            entry: $entry,
            hasTraveled: 'yes',
        ))->execute();

        $this->assertEquals('yes', $result->has_traveled_today);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'has_traveled_logged' && $job->user->id === $user->id;
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
    public function it_logs_traveled_with_no(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->for($journal)->create([
            'has_traveled_today' => null,
        ]);

        $result = (new LogTravelledToday(
            user: $user,
            entry: $entry,
            hasTraveled: 'no',
        ))->execute();

        $this->assertEquals('no', $result->has_traveled_today);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'has_traveled_logged' && $job->user->id === $user->id;
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
    public function it_throws_when_journal_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Journal not found');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $entry = JournalEntry::factory()->for($journal)->create();

        (new LogTravelledToday(
            user: $user,
            entry: $entry,
            hasTraveled: 'yes',
        ))->execute();
    }

    #[Test]
    public function it_throws_when_has_traveled_is_not_yes_or_no(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('hasTraveled must be either "yes" or "no"');

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->for($journal)->create();

        (new LogTravelledToday(
            user: $user,
            entry: $entry,
            hasTraveled: 'maybe',
        ))->execute();
    }
}
