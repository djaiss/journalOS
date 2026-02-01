<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\ToggleJournalEntryEdition;
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

final class ToggleJournalEntryEditionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_toggles_entry_edition_state(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'can_edit_past' => true,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'is_edited' => true,
        ]);

        $updatedEntry = new ToggleJournalEntryEdition(
            user: $user,
            entry: $entry,
        )->execute();

        $this->assertFalse($updatedEntry->is_edited);

        $this->assertDatabaseHas('journal_entries', [
            'id' => $entry->id,
            'is_edited' => false,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job): bool => (
                $job->action === 'journal_entry_edition_toggled'
                && $job->user->is($user)
                && $job->journal?->is($journal)
            ),
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: fn (UpdateUserLastActivityDate $job): bool => $job->user->is($user),
        );
    }

    #[Test]
    public function it_throws_when_edit_state_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'can_edit_past' => true,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'is_edited' => true,
        ]);

        $entry->setRawAttributes(array_merge(
            $entry->getAttributes(),
            ['is_edited' => null],
        ));

        new ToggleJournalEntryEdition(
            user: $user,
            entry: $entry,
        )->execute();
    }

    #[Test]
    public function it_throws_when_entry_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new ToggleJournalEntryEdition(
            user: $user,
            entry: $entry,
        )->execute();
    }
}
