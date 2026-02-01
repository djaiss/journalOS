<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\RenameJournal;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class RenameJournalTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_renames_a_journal(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'name' => 'Dunder Mifflin',
        ]);

        $updatedJournal = new RenameJournal(
            user: $user,
            journal: $journal,
            name: 'Threat Level Midnight',
        )->execute();

        $this->assertEquals('Threat Level Midnight', $updatedJournal->name);
        $this->assertEquals($journal->id . '-threat-level-midnight', $updatedJournal->slug);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job) => (
                $job->action === 'journal_rename'
                && $job->user->id === $user->id
                && $job->journal?->id === $journal->id
            ),
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: fn (UpdateUserLastActivityDate $job) => $job->user->id === $user->id,
        );
    }

    #[Test]
    public function it_throws_an_exception_if_name_contains_special_characters(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        new RenameJournal(
            user: $user,
            journal: $journal,
            name: 'Dunder@ / Mifflin!',
        )->execute();
    }

    #[Test]
    public function it_throws_an_exception_if_journal_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $otherJournal = Journal::factory()->create();

        new RenameJournal(
            user: $user,
            journal: $otherJournal,
            name: 'Valid Journal Name',
        )->execute();
    }
}
