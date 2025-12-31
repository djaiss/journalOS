<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogHasWorked;
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

final class LogHasWorkedTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_has_worked_with_yes(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'worked' => null,
        ]);

        $result = (new LogHasWorked(
            user: $user,
            entry: $entry,
            hasWorked: 'yes',
        ))->execute();

        $this->assertEquals('yes', $result->worked);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'has_worked_logged' && $job->user->id === $user->id;
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
    public function it_logs_has_worked_with_no(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'worked' => null,
        ]);

        $result = (new LogHasWorked(
            user: $user,
            entry: $entry,
            hasWorked: 'no',
        ))->execute();

        $this->assertEquals('no', $result->worked);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'has_worked_logged' && $job->user->id === $user->id;
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
    public function it_throws_when_journal_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Journal not found');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->for($otherUser)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        (new LogHasWorked(
            user: $user,
            entry: $entry,
            hasWorked: 'yes',
        ))->execute();
    }

    #[Test]
    public function it_throws_when_has_worked_is_not_yes_or_no(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('hasWorked must be either "yes" or "no"');

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        (new LogHasWorked(
            user: $user,
            entry: $entry,
            hasWorked: 'maybe',
        ))->execute();
    }
}
