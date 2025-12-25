<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogSleep;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Exception;
use Illuminate\Support\Facades\Queue;

final class LogSleepTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_sleep_with_valid_times(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        $action = new LogSleep(
            user: $user,
            entry: $entry,
            bedtime: '22:30',
            wakeUpTime: '06:45',
        );

        $result = $action->execute();

        $this->assertEquals('22:30', $result->bedtime);
        $this->assertEquals('06:45', $result->wake_up_time);
        $this->assertEquals('495', $result->sleep_duration_in_minutes);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'sleep_logged' && $job->user->id === $user->id;
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
    public function it_throws_for_invalid_bedtime_format(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid bedtime format. Expected HH:MM');

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        $action = new LogSleep(
            user: $user,
            entry: $entry,
            bedtime: '10pm',
            wakeUpTime: '06:45',
        );

        $action->execute();
    }

    #[Test]
    public function it_throws_for_invalid_wake_up_time_format(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid wake-up time format. Expected HH:MM');

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        $action = new LogSleep(
            user: $user,
            entry: $entry,
            bedtime: '22:30',
            wakeUpTime: '6:45am',
        );

        $action->execute();
    }

    #[Test]
    public function it_handles_wake_up_next_day(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        $action = new LogSleep(
            user: $user,
            entry: $entry,
            bedtime: '23:30',
            wakeUpTime: '07:00',
        );

        $result = $action->execute();

        $this->assertEquals('450', $result->sleep_duration_in_minutes);
    }
}
