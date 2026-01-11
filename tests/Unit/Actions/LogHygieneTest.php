<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogHygiene;
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

final class LogHygieneTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_hygiene_with_showered(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = (new LogHygiene(
            user: $user,
            entry: $entry,
            showered: 'yes',
            brushedTeeth: null,
            skincare: null,
        ))->execute();

        $this->assertEquals('yes', $entry->moduleHygiene->showered);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'hygiene_logged' && $job->user->id === $user->id;
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
    public function it_logs_hygiene_with_brushed_teeth(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = (new LogHygiene(
            user: $user,
            entry: $entry,
            showered: null,
            brushedTeeth: 'am',
            skincare: null,
        ))->execute();

        $this->assertEquals('am', $entry->moduleHygiene->brushed_teeth);
    }

    #[Test]
    public function it_logs_hygiene_with_skincare(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = (new LogHygiene(
            user: $user,
            entry: $entry,
            showered: null,
            brushedTeeth: null,
            skincare: 'no',
        ))->execute();

        $this->assertEquals('no', $entry->moduleHygiene->skincare);
    }

    #[Test]
    public function it_throws_validation_exception_for_invalid_hygiene_value(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        (new LogHygiene(
            user: $user,
            entry: $entry,
            showered: 'maybe',
            brushedTeeth: null,
            skincare: null,
        ))->execute();
    }

    #[Test]
    public function it_throws_validation_exception_when_no_values_provided(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        (new LogHygiene(
            user: $user,
            entry: $entry,
            showered: null,
            brushedTeeth: null,
            skincare: null,
        ))->execute();
    }

    #[Test]
    public function it_throws_exception_when_user_does_not_own_journal(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $anotherUser->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        (new LogHygiene(
            user: $user,
            entry: $entry,
            showered: 'yes',
            brushedTeeth: null,
            skincare: null,
        ))->execute();
    }
}
