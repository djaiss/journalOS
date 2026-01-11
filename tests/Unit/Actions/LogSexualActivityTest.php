<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogSexualActivity;
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

final class LogSexualActivityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_had_sexual_activity_yes(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogSexualActivity(
            user: $user,
            entry: $entry,
            hadSexualActivity: 'yes',
            sexualActivityType: null,
        )->execute();

        $this->assertEquals('yes', $entry->moduleSexualActivity->had_sexual_activity);

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
    public function it_logs_had_sexual_activity_no(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogSexualActivity(
            user: $user,
            entry: $entry,
            hadSexualActivity: 'no',
            sexualActivityType: null,
        )->execute();

        $this->assertEquals('no', $entry->moduleSexualActivity->had_sexual_activity);
    }

    #[Test]
    public function it_logs_sexual_activity_type_solo(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogSexualActivity(
            user: $user,
            entry: $entry,
            hadSexualActivity: null,
            sexualActivityType: 'solo',
        )->execute();

        $this->assertEquals('solo', $entry->moduleSexualActivity->sexual_activity_type);
    }

    #[Test]
    public function it_logs_sexual_activity_type_with_partner(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogSexualActivity(
            user: $user,
            entry: $entry,
            hadSexualActivity: null,
            sexualActivityType: 'with-partner',
        )->execute();

        $this->assertEquals('with-partner', $entry->moduleSexualActivity->sexual_activity_type);
    }

    #[Test]
    public function it_logs_sexual_activity_type_intimate_contact(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogSexualActivity(
            user: $user,
            entry: $entry,
            hadSexualActivity: null,
            sexualActivityType: 'intimate-contact',
        )->execute();

        $this->assertEquals('intimate-contact', $entry->moduleSexualActivity->sexual_activity_type);
    }

    #[Test]
    public function it_logs_both_fields(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogSexualActivity(
            user: $user,
            entry: $entry,
            hadSexualActivity: 'yes',
            sexualActivityType: 'with-partner',
        )->execute();

        $this->assertEquals('yes', $entry->moduleSexualActivity->had_sexual_activity);
        $this->assertEquals('with-partner', $entry->moduleSexualActivity->sexual_activity_type);
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

        new LogSexualActivity(
            user: $user,
            entry: $entry,
            hadSexualActivity: 'yes',
            sexualActivityType: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_all_values_are_null(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogSexualActivity(
            user: $user,
            entry: $entry,
            hadSexualActivity: null,
            sexualActivityType: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_had_sexual_activity_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogSexualActivity(
            user: $user,
            entry: $entry,
            hadSexualActivity: 'invalid',
            sexualActivityType: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_sexual_activity_type_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogSexualActivity(
            user: $user,
            entry: $entry,
            hadSexualActivity: null,
            sexualActivityType: 'invalid',
        )->execute();
    }
}
