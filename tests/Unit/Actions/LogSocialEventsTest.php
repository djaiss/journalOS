<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\LogSocialEvents;
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

final class LogSocialEventsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_logs_social_events_with_type_tone_and_duration(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $result = new LogSocialEvents(
            user: $user,
            entry: $entry,
            eventType: 'friends',
            tone: 'positive',
            duration: 'short',
        )->execute();

        $this->assertEquals('friends', $result->moduleSocialEvents?->event_type);
        $this->assertEquals('positive', $result->moduleSocialEvents?->tone);
        $this->assertEquals('short', $result->moduleSocialEvents?->duration);
        $this->assertDatabaseHas('module_social_events', [
            'journal_entry_id' => $entry->id,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job) => (
                $job->action === 'social_events_logged'
                && $job->user->id === $user->id
            ),
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
    public function it_logs_social_events_with_event_type_only(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $result = new LogSocialEvents(
            user: $user,
            entry: $entry,
            eventType: 'work',
            tone: null,
            duration: null,
        )->execute();

        $this->assertEquals('work', $result->moduleSocialEvents?->event_type);
        $this->assertNull($result->moduleSocialEvents?->tone);
        $this->assertNull($result->moduleSocialEvents?->duration);
    }

    #[Test]
    public function it_throws_validation_exception_for_invalid_event_type(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogSocialEvents(
            user: $user,
            entry: $entry,
            eventType: 'invalid',
            tone: 'positive',
            duration: null,
        )->execute();
    }

    #[Test]
    public function it_throws_validation_exception_for_invalid_tone(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogSocialEvents(
            user: $user,
            entry: $entry,
            eventType: 'friends',
            tone: 'invalid',
            duration: null,
        )->execute();
    }

    #[Test]
    public function it_throws_validation_exception_for_invalid_duration(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogSocialEvents(
            user: $user,
            entry: $entry,
            eventType: 'friends',
            tone: 'positive',
            duration: 'invalid',
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

        new LogSocialEvents(
            user: $user,
            entry: $entry,
            eventType: 'friends',
            tone: 'neutral',
            duration: null,
        )->execute();
    }
}
