<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\LogPrimaryObligation;
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

final class LogPrimaryObligationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_logs_primary_obligation_with_work(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogPrimaryObligation(
            user: $user,
            entry: $entry,
            primaryObligation: 'work',
        )->execute();

        $this->assertEquals('work', $entry->modulePrimaryObligation?->primary_obligation);
        $this->assertDatabaseHas('module_primary_obligation', [
            'journal_entry_id' => $entry->id,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job) => $job->action === 'primary_obligation_logged' && $job->user->id === $user->id,
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
    public function it_logs_primary_obligation_with_family(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogPrimaryObligation(
            user: $user,
            entry: $entry,
            primaryObligation: 'family',
        )->execute();

        $this->assertEquals('family', $entry->modulePrimaryObligation?->primary_obligation);
        $this->assertDatabaseHas('module_primary_obligation', [
            'journal_entry_id' => $entry->id,
        ]);
    }

    #[Test]
    public function it_logs_primary_obligation_with_personal(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogPrimaryObligation(
            user: $user,
            entry: $entry,
            primaryObligation: 'personal',
        )->execute();

        $this->assertEquals('personal', $entry->modulePrimaryObligation?->primary_obligation);
        $this->assertDatabaseHas('module_primary_obligation', [
            'journal_entry_id' => $entry->id,
        ]);
    }

    #[Test]
    public function it_logs_primary_obligation_with_health(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogPrimaryObligation(
            user: $user,
            entry: $entry,
            primaryObligation: 'health',
        )->execute();

        $this->assertEquals('health', $entry->modulePrimaryObligation?->primary_obligation);
        $this->assertDatabaseHas('module_primary_obligation', [
            'journal_entry_id' => $entry->id,
        ]);
    }

    #[Test]
    public function it_logs_primary_obligation_with_travel(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogPrimaryObligation(
            user: $user,
            entry: $entry,
            primaryObligation: 'travel',
        )->execute();

        $this->assertEquals('travel', $entry->modulePrimaryObligation?->primary_obligation);
        $this->assertDatabaseHas('module_primary_obligation', [
            'journal_entry_id' => $entry->id,
        ]);
    }

    #[Test]
    public function it_logs_primary_obligation_with_none(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogPrimaryObligation(
            user: $user,
            entry: $entry,
            primaryObligation: 'none',
        )->execute();

        $this->assertEquals('none', $entry->modulePrimaryObligation?->primary_obligation);
        $this->assertDatabaseHas('module_primary_obligation', [
            'journal_entry_id' => $entry->id,
        ]);
    }

    #[Test]
    public function it_throws_validation_exception_for_invalid_primary_obligation_value(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogPrimaryObligation(
            user: $user,
            entry: $entry,
            primaryObligation: 'invalid',
        )->execute();
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

        new LogPrimaryObligation(
            user: $user,
            entry: $entry,
            primaryObligation: 'work',
        )->execute();
    }
}
