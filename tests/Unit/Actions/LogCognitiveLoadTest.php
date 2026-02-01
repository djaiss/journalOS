<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\LogCognitiveLoad;
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

final class LogCognitiveLoadTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_logs_cognitive_load_with_details(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogCognitiveLoad(
            user: $user,
            entry: $entry,
            cognitiveLoad: 'high',
            primarySource: 'work',
            loadQuality: 'productive',
        )->execute();

        $this->assertEquals('high', $entry->moduleCognitiveLoad->cognitive_load);
        $this->assertEquals('work', $entry->moduleCognitiveLoad->primary_source);
        $this->assertEquals('productive', $entry->moduleCognitiveLoad->load_quality);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job) => $job->action === 'cognitive_load_logged' && $job->user->id === $user->id,
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
    public function it_logs_cognitive_load_without_optional_details(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogCognitiveLoad(
            user: $user,
            entry: $entry,
            cognitiveLoad: 'very low',
            primarySource: null,
            loadQuality: null,
        )->execute();

        $this->assertEquals('very low', $entry->moduleCognitiveLoad->cognitive_load);
        $this->assertNull($entry->moduleCognitiveLoad->primary_source);
        $this->assertNull($entry->moduleCognitiveLoad->load_quality);
    }

    #[Test]
    public function it_throws_validation_exception_for_invalid_cognitive_load(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogCognitiveLoad(
            user: $user,
            entry: $entry,
            cognitiveLoad: 'invalid',
            primarySource: null,
            loadQuality: null,
        )->execute();
    }

    #[Test]
    public function it_throws_validation_exception_for_invalid_primary_source(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogCognitiveLoad(
            user: $user,
            entry: $entry,
            cognitiveLoad: 'low',
            primarySource: 'invalid',
            loadQuality: null,
        )->execute();
    }

    #[Test]
    public function it_throws_validation_exception_for_invalid_load_quality(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogCognitiveLoad(
            user: $user,
            entry: $entry,
            cognitiveLoad: 'high',
            primarySource: null,
            loadQuality: 'invalid',
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

        new LogCognitiveLoad(
            user: $user,
            entry: $entry,
            cognitiveLoad: 'low',
            primarySource: null,
            loadQuality: null,
        )->execute();
    }
}
