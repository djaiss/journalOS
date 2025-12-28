<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\DestroyJournal;
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

final class DestroyJournalTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_a_journal(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create([
            'name' => 'Dunder Mifflin',
        ]);
        $entry = JournalEntry::factory()->for($journal)->create();

        (new DestroyJournal(
            user: $user,
            journal: $journal,
        ))->execute();

        $this->assertDatabaseMissing('journals', [
            'id' => $journal->id,
        ]);

        $this->assertDatabaseMissing('journal_entries', [
            'id' => $entry->id,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user, $journal): bool {
                return $job->action === 'journal_deletion'
                    && $job->user->id === $user->id
                    && $job->journal?->id === $journal->id;
            },
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: function (UpdateUserLastActivityDate $job) use ($user): bool {
                return $job->user->id === $user->id;
            },
        );
    }

    #[Test]
    public function it_throws_an_exception_if_journal_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $otherJournal = Journal::factory()->create();

        (new DestroyJournal(
            user: $user,
            journal: $otherJournal,
        ))->execute();
    }
}
