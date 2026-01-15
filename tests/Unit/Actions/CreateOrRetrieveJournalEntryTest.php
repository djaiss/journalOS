<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreateOrRetrieveJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Layout;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class CreateOrRetrieveJournalEntryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_creates_an_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'name' => 'Dunder Mifflin Journal',
        ]);

        $entry = (new CreateOrRetrieveJournalEntry(
            user: $user,
            journal: $journal,
            day: 1,
            month: 1,
            year: 2024,
        ))->execute();

        $this->assertDatabaseHas('journal_entries', [
            'id' => $entry->id,
            'journal_id' => $journal->id,
            'day' => 1,
            'month' => 1,
            'year' => 2024,
        ]);

        $this->assertInstanceOf(JournalEntry::class, $entry);

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: function (UpdateUserLastActivityDate $job) use ($user): bool {
                return $job->user->id === $user->id;
            },
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'entry_creation'
                    && $job->user->id === $user->id
                    && str_contains($job->description, 'Dunder Mifflin Journal');
            },
        );
    }

    #[Test]
    public function it_retrieves_an_existing_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $existingEntry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'day' => 1,
            'month' => 1,
            'year' => 2024,
        ]);

        $entry = (new CreateOrRetrieveJournalEntry(
            user: $user,
            journal: $journal,
            day: 1,
            month: 1,
            year: 2024,
        ))->execute();

        $this->assertEquals($existingEntry->id, $entry->id);
    }

    #[Test]
    public function it_assigns_the_active_layout_when_creating_an_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
            'is_active' => true,
        ]);

        $entry = (new CreateOrRetrieveJournalEntry(
            user: $user,
            journal: $journal,
            day: 5,
            month: 4,
            year: 2024,
        ))->execute();

        $this->assertEquals($layout->id, $entry->layout_id);
    }

    #[Test]
    public function it_fails_if_journal_doesnt_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create();

        (new CreateOrRetrieveJournalEntry(
            user: $user,
            journal: $journal,
            day: 1,
            month: 1,
            year: 2024,
        ))->execute();
    }

    #[Test]
    public function it_fails_if_date_is_invalid(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid date');

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        (new CreateOrRetrieveJournalEntry(
            user: $user,
            journal: $journal,
            day: 31,
            month: 2,
            year: 2024,
        ))->execute();
    }
}
