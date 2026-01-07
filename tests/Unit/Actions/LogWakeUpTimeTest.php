<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogWakeUpTime;
use App\Jobs\CalculateSleepDuration;
use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LogWakeUpTimeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_wake_up_time_with_valid_time(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        (new LogWakeUpTime(
            user: $user,
            entry: $entry,
            wakeUpTime: '06:45',
        ))->execute();

        $entry->refresh();
        $this->assertNotNull($entry->moduleSleep);
        $this->assertEquals('06:45', $entry->moduleSleep->wake_up_time);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'sleep_wake_up_logged' && $job->user->id === $user->id;
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
            job: CalculateSleepDuration::class,
            callback: function (CalculateSleepDuration $job) use ($entry): bool {
                return $job->entry->id === $entry->id;
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
    public function it_throws_for_invalid_wake_up_time_format(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid wake-up time format. Expected HH:MM');

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        $action = new LogWakeUpTime(
            user: $user,
            entry: $entry,
            wakeUpTime: '6:45am',
        );

        $action->execute();
    }
}
