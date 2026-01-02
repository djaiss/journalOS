<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogWorkMode;
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

final class LogWorkModeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_work_mode_with_on_site(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'work_mode' => null,
        ]);

        $result = (new LogWorkMode(
            user: $user,
            entry: $entry,
            workMode: 'on-site',
        ))->execute();

        $this->assertEquals('on-site', $result->work_mode);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'work_mode_logged' && $job->user->id === $user->id;
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
    public function it_logs_work_mode_with_remote(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'work_mode' => null,
        ]);

        $result = (new LogWorkMode(
            user: $user,
            entry: $entry,
            workMode: 'remote',
        ))->execute();

        $this->assertEquals('remote', $result->work_mode);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'work_mode_logged' && $job->user->id === $user->id;
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
    public function it_logs_work_mode_with_hybrid(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'work_mode' => null,
        ]);

        $result = (new LogWorkMode(
            user: $user,
            entry: $entry,
            workMode: 'hybrid',
        ))->execute();

        $this->assertEquals('hybrid', $result->work_mode);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'work_mode_logged' && $job->user->id === $user->id;
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

        (new LogWorkMode(
            user: $user,
            entry: $entry,
            workMode: 'on-site',
        ))->execute();
    }

    #[Test]
    public function it_throws_when_work_mode_is_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('workMode must be either "on-site", "remote", or "hybrid"');

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        (new LogWorkMode(
            user: $user,
            entry: $entry,
            workMode: 'invalid',
        ))->execute();
    }
}
