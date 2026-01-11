<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogSleep;
use App\Jobs\CalculateSleepDuration;
use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LogSleepTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_bedtime(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogSleep(
            user: $user,
            entry: $entry,
            bedtime: '23:30',
            wakeUpTime: null,
        )->execute();

        $this->assertEquals('23:30', $entry->moduleSleep->bedtime);

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
    public function it_logs_wake_up_time(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogSleep(
            user: $user,
            entry: $entry,
            bedtime: null,
            wakeUpTime: '07:00',
        )->execute();

        $this->assertEquals('07:00', $entry->moduleSleep->wake_up_time);
    }

    #[Test]
    public function it_logs_both_bedtime_and_wake_up_time(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogSleep(
            user: $user,
            entry: $entry,
            bedtime: '23:30',
            wakeUpTime: '07:00',
        )->execute();

        $this->assertEquals('23:30', $entry->moduleSleep->bedtime);
        $this->assertEquals('07:00', $entry->moduleSleep->wake_up_time);
    }

    #[Test]
    public function it_throws_when_entry_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogSleep(
            user: $user,
            entry: $entry,
            bedtime: '23:30',
            wakeUpTime: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_both_values_are_null(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogSleep(
            user: $user,
            entry: $entry,
            bedtime: null,
            wakeUpTime: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_bedtime_format_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogSleep(
            user: $user,
            entry: $entry,
            bedtime: 'invalid',
            wakeUpTime: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_wake_up_time_format_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogSleep(
            user: $user,
            entry: $entry,
            bedtime: null,
            wakeUpTime: 'invalid',
        )->execute();
    }

    #[Test]
    public function it_throws_when_bedtime_has_invalid_hour(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogSleep(
            user: $user,
            entry: $entry,
            bedtime: '25:00',
            wakeUpTime: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_wake_up_time_has_invalid_minutes(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogSleep(
            user: $user,
            entry: $entry,
            bedtime: null,
            wakeUpTime: '07:61',
        )->execute();
    }
}
