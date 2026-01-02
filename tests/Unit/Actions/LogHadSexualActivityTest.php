<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogHadSexualActivity;
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

final class LogHadSexualActivityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_sexual_activity_with_yes(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'had_sexual_activity' => null,
        ]);

        $result = (new LogHadSexualActivity(
            user: $user,
            entry: $entry,
            hadSexualActivity: 'yes',
        ))->execute();

        $this->assertEquals('yes', $result->had_sexual_activity);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'sexual_activity_logged' && $job->user->id === $user->id;
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
    public function it_logs_sexual_activity_with_no(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'had_sexual_activity' => null,
        ]);

        $result = (new LogHadSexualActivity(
            user: $user,
            entry: $entry,
            hadSexualActivity: 'no',
        ))->execute();

        $this->assertEquals('no', $result->had_sexual_activity);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'sexual_activity_logged' && $job->user->id === $user->id;
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
        $journal = Journal::factory()->for($otherUser)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        (new LogHadSexualActivity(
            user: $user,
            entry: $entry,
            hadSexualActivity: 'yes',
        ))->execute();
    }

    #[Test]
    public function it_throws_when_had_sexual_activity_is_not_yes_or_no(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('hadSexualActivity must be either "yes" or "no"');

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        (new LogHadSexualActivity(
            user: $user,
            entry: $entry,
            hadSexualActivity: 'maybe',
        ))->execute();
    }
}
