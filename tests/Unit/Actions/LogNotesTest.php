<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogNotes;
use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LogNotesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_notes(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $result = (new LogNotes(
            user: $user,
            entry: $entry,
            notes: '<p>Today was great!</p>',
        ))->execute();

        $this->assertNotNull($result->notes);
        $this->assertStringContainsString('Today was great!', $result->notes->toPlainText());
        $this->assertDatabaseHas('rich_texts', [
            'record_type' => JournalEntry::class,
            'record_id' => $entry->id,
            'field' => 'notes',
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'journal_entry_notes_logged' && $job->user->id === $user->id;
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
    public function it_sanitizes_html(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $maliciousContent = '<script>alert("XSS")</script><p>Safe content</p>';

        $result = (new LogNotes(
            user: $user,
            entry: $entry,
            notes: $maliciousContent,
        ))->execute();

        $this->assertNotNull($result->notes);
        $this->assertStringNotContainsString('<script>', $result->notes->toHtml());
        $this->assertStringContainsString('Safe content', $result->notes->toPlainText());
    }

    #[Test]
    public function it_updates_existing_notes(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'notes' => '<p>Old notes</p>',
        ]);

        $result = (new LogNotes(
            user: $user,
            entry: $entry,
            notes: '<p>New notes</p>',
        ))->execute();

        $this->assertStringContainsString('New notes', $result->notes->toPlainText());
        $this->assertStringNotContainsString('Old notes', $result->notes->toPlainText());
    }

    #[Test]
    public function it_throws_exception_when_user_does_not_own_entry(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $this->expectException(ModelNotFoundException::class);

        (new LogNotes(
            user: $user,
            entry: $entry,
            notes: '<p>Test notes</p>',
        ))->execute();
    }
}
