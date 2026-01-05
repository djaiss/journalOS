<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogBedTime;
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

final class LogBedTimeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_bedtime_with_valid_time(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'sleep_duration_in_minutes' => '300',
        ]);

        $result = (new LogBedTime(
            user: $user,
            entry: $entry,
            bedtime: '22:30',
        ))->execute();

        $this->assertEquals('22:30', $result->bedtime);
        $this->assertEquals('300', $result->sleep_duration_in_minutes);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'sleep_bedtime_logged' && $job->user->id === $user->id;
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
    public function it_throws_for_invalid_bedtime_format(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid bedtime format. Expected HH:MM');

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        $action = new LogBedTime(
            user: $user,
            entry: $entry,
            bedtime: '10pm',
        );

        $action->execute();
    }

    #[Test]
    public function it_throws_when_bedtime_is_too_long(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid bedtime format. Expected HH:MM');

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        (new LogBedTime(
            user: $user,
            entry: $entry,
            bedtime: str_repeat('1', 6),
        ))->execute();
    }
}
