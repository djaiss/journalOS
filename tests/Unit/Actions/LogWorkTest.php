<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\LogWork;
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

final class LogWorkTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_logs_worked_yes(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogWork(
            user: $user,
            entry: $entry,
            worked: 'yes',
            workMode: null,
            workLoad: null,
            workProcrastinated: null,
        )->execute();

        $this->assertEquals('yes', $entry->moduleWork->worked);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'work_logged' && $job->user->id === $user->id;
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
    public function it_logs_worked_no(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogWork(
            user: $user,
            entry: $entry,
            worked: 'no',
            workMode: null,
            workLoad: null,
            workProcrastinated: null,
        )->execute();

        $this->assertEquals('no', $entry->moduleWork->worked);
    }

    #[Test]
    public function it_logs_work_mode_on_site(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogWork(
            user: $user,
            entry: $entry,
            worked: null,
            workMode: 'on-site',
            workLoad: null,
            workProcrastinated: null,
        )->execute();

        $this->assertEquals('on-site', $entry->moduleWork->work_mode);
    }

    #[Test]
    public function it_logs_work_mode_remote(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogWork(
            user: $user,
            entry: $entry,
            worked: null,
            workMode: 'remote',
            workLoad: null,
            workProcrastinated: null,
        )->execute();

        $this->assertEquals('remote', $entry->moduleWork->work_mode);
    }

    #[Test]
    public function it_logs_work_mode_hybrid(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogWork(
            user: $user,
            entry: $entry,
            worked: null,
            workMode: 'hybrid',
            workLoad: null,
            workProcrastinated: null,
        )->execute();

        $this->assertEquals('hybrid', $entry->moduleWork->work_mode);
    }

    #[Test]
    public function it_logs_work_load_light(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogWork(
            user: $user,
            entry: $entry,
            worked: null,
            workMode: null,
            workLoad: 'light',
            workProcrastinated: null,
        )->execute();

        $this->assertEquals('light', $entry->moduleWork->work_load);
    }

    #[Test]
    public function it_logs_work_load_medium(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogWork(
            user: $user,
            entry: $entry,
            worked: null,
            workMode: null,
            workLoad: 'medium',
            workProcrastinated: null,
        )->execute();

        $this->assertEquals('medium', $entry->moduleWork->work_load);
    }

    #[Test]
    public function it_logs_work_load_heavy(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogWork(
            user: $user,
            entry: $entry,
            worked: null,
            workMode: null,
            workLoad: 'heavy',
            workProcrastinated: null,
        )->execute();

        $this->assertEquals('heavy', $entry->moduleWork->work_load);
    }

    #[Test]
    public function it_logs_work_procrastinated_yes(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogWork(
            user: $user,
            entry: $entry,
            worked: null,
            workMode: null,
            workLoad: null,
            workProcrastinated: 'yes',
        )->execute();

        $this->assertEquals('yes', $entry->moduleWork->work_procrastinated);
    }

    #[Test]
    public function it_logs_work_procrastinated_no(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogWork(
            user: $user,
            entry: $entry,
            worked: null,
            workMode: null,
            workLoad: null,
            workProcrastinated: 'no',
        )->execute();

        $this->assertEquals('no', $entry->moduleWork->work_procrastinated);
    }

    #[Test]
    public function it_logs_all_fields(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogWork(
            user: $user,
            entry: $entry,
            worked: 'yes',
            workMode: 'remote',
            workLoad: 'heavy',
            workProcrastinated: 'no',
        )->execute();

        $this->assertEquals('yes', $entry->moduleWork->worked);
        $this->assertEquals('remote', $entry->moduleWork->work_mode);
        $this->assertEquals('heavy', $entry->moduleWork->work_load);
        $this->assertEquals('no', $entry->moduleWork->work_procrastinated);
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

        new LogWork(
            user: $user,
            entry: $entry,
            worked: 'yes',
            workMode: null,
            workLoad: null,
            workProcrastinated: null,
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

        new LogWork(
            user: $user,
            entry: $entry,
            worked: null,
            workMode: null,
            workLoad: null,
            workProcrastinated: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_worked_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogWork(
            user: $user,
            entry: $entry,
            worked: 'invalid',
            workMode: null,
            workLoad: null,
            workProcrastinated: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_work_mode_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogWork(
            user: $user,
            entry: $entry,
            worked: null,
            workMode: 'invalid',
            workLoad: null,
            workProcrastinated: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_work_load_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogWork(
            user: $user,
            entry: $entry,
            worked: null,
            workMode: null,
            workLoad: 'invalid',
            workProcrastinated: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_work_procrastinated_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogWork(
            user: $user,
            entry: $entry,
            worked: null,
            workMode: null,
            workLoad: null,
            workProcrastinated: 'invalid',
        )->execute();
    }
}
