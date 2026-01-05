<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogSexualActivityType;
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

final class LogSexualActivityTypeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_sexual_activity_type_with_solo(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'sexual_activity_type' => null,
        ]);

        $result = (new LogSexualActivityType(
            user: $user,
            entry: $entry,
            sexualActivityType: 'solo',
        ))->execute();

        $this->assertEquals('solo', $result->sexual_activity_type);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'sexual_activity_type_logged' && $job->user->id === $user->id;
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
    public function it_logs_sexual_activity_type_with_with_partner(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'sexual_activity_type' => null,
        ]);

        $result = (new LogSexualActivityType(
            user: $user,
            entry: $entry,
            sexualActivityType: 'with-partner',
        ))->execute();

        $this->assertEquals('with-partner', $result->sexual_activity_type);
    }

    #[Test]
    public function it_logs_sexual_activity_type_with_intimate_contact(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'sexual_activity_type' => null,
        ]);

        $result = (new LogSexualActivityType(
            user: $user,
            entry: $entry,
            sexualActivityType: 'intimate-contact',
        ))->execute();

        $this->assertEquals('intimate-contact', $result->sexual_activity_type);
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

        (new LogSexualActivityType(
            user: $user,
            entry: $entry,
            sexualActivityType: 'solo',
        ))->execute();
    }

    #[Test]
    public function it_throws_when_sexual_activity_type_is_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('sexualActivityType must be one of: solo, with-partner, intimate-contact');

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        (new LogSexualActivityType(
            user: $user,
            entry: $entry,
            sexualActivityType: 'invalid',
        ))->execute();
    }

    #[Test]
    public function it_throws_when_sexual_activity_type_is_too_long(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        (new LogSexualActivityType(
            user: $user,
            entry: $entry,
            sexualActivityType: str_repeat('a', 256),
        ))->execute();
    }
}
