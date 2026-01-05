<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreateJournal;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class CreateJournalTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_journal(): void
    {
        Queue::fake();
        Carbon::setTestNow(Carbon::parse('2025-03-17 10:00:00'));

        $user = User::factory()->create();

        $journal = (new CreateJournal(
            user: $user,
            name: 'Dunder Mifflin',
        ))->execute();

        $this->assertEquals('Dunder Mifflin', $journal->name);
        $this->assertEquals($journal->id . '-dunder-mifflin', $journal->slug);

        $this->assertInstanceOf(Journal::class, $journal);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'journal_creation' && $job->user->id === $user->id;
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
    public function it_throws_an_exception_if_journal_name_contains_special_characters(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();

        (new CreateJournal(
            user: $user,
            name: 'Dunder@ / Mifflin!',
        ))->execute();
    }

    #[Test]
    public function it_throws_when_journal_name_is_too_long(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();

        (new CreateJournal(
            user: $user,
            name: str_repeat('a', 256),
        ))->execute();
    }
}
