<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\LogReading;
use App\Enums\BookStatus;
use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Book;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleReading;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LogReadingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_logs_reading_data(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogReading(
            user: $user,
            entry: $entry,
            didReadToday: 'yes',
            readingAmount: 'one solid session',
            mentalState: 'stimulated',
            readingFeel: 'engaging',
            wantContinue: 'strongly',
            readingLimit: 'time',
        )->execute();

        $this->assertEquals('yes', $entry->moduleReading->did_read_today);
        $this->assertEquals('one solid session', $entry->moduleReading->reading_amount);
        $this->assertEquals('stimulated', $entry->moduleReading->mental_state);
        $this->assertEquals('engaging', $entry->moduleReading->reading_feel);
        $this->assertEquals('strongly', $entry->moduleReading->want_continue);
        $this->assertEquals('time', $entry->moduleReading->reading_limit);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job) => $job->action === 'reading_logged' && $job->user->id === $user->id,
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
    public function it_clears_reading_data_when_not_reading_today(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleReading::factory()->create([
            'journal_entry_id' => $entry->id,
            'did_read_today' => 'yes',
            'reading_amount' => 'multiple sessions',
            'mental_state' => 'neutral',
        ]);
        $book = Book::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry->books()->attach($book, ['status' => BookStatus::CONTINUED->value]);

        $entry = new LogReading(
            user: $user,
            entry: $entry,
            didReadToday: 'no',
            readingAmount: null,
            mentalState: null,
            readingFeel: null,
            wantContinue: null,
            readingLimit: null,
        )->execute();

        $this->assertEquals('no', $entry->moduleReading->did_read_today);
        $this->assertNull($entry->moduleReading->reading_amount);
        $this->assertNull($entry->moduleReading->mental_state);
        $this->assertEmpty($entry->books);
    }

    #[Test]
    public function it_throws_validation_exception_for_invalid_reading_amount(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogReading(
            user: $user,
            entry: $entry,
            didReadToday: 'yes',
            readingAmount: 'invalid',
            mentalState: null,
            readingFeel: null,
            wantContinue: null,
            readingLimit: null,
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

        new LogReading(
            user: $user,
            entry: $entry,
            didReadToday: 'yes',
            readingAmount: 'a few pages',
            mentalState: null,
            readingFeel: null,
            wantContinue: null,
            readingLimit: null,
        )->execute();
    }
}
