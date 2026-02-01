<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\LogHadKidsToday;
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

final class LogHadKidsTodayTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_logs_kids_today_with_yes(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogHadKidsToday(
            user: $user,
            entry: $entry,
            hadKidsToday: 'yes',
        )->execute();

        $this->assertEquals('yes', $entry->moduleKids->had_kids_today);
        $this->assertDatabaseHas('module_kids', [
            'journal_entry_id' => $entry->id,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job) => $job->action === 'kids_today_logged' && $job->user->id === $user->id,
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: fn (UpdateUserLastActivityDate $job) => $job->user->id === $user->id,
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: CheckPresenceOfContentInJournalEntry::class,
            callback: fn (CheckPresenceOfContentInJournalEntry $job) => $job->entry->id === $entry->id,
        );
    }

    #[Test]
    public function it_logs_kids_today_with_no(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogHadKidsToday(
            user: $user,
            entry: $entry,
            hadKidsToday: 'no',
        )->execute();

        $this->assertEquals('no', $entry->moduleKids->had_kids_today);
        $this->assertDatabaseHas('module_kids', [
            'journal_entry_id' => $entry->id,
        ]);
    }

    #[Test]
    public function it_throws_when_journal_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Journal not found');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogHadKidsToday(
            user: $user,
            entry: $entry,
            hadKidsToday: 'yes',
        )->execute();
    }

    #[Test]
    public function it_throws_when_kids_today_is_not_yes_or_no(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('hadKidsToday must be either "yes" or "no"');

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogHadKidsToday(
            user: $user,
            entry: $entry,
            hadKidsToday: 'maybe',
        )->execute();
    }
}
