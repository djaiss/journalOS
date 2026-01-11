<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogShopping;
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

final class LogShoppingForTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_shopping_for(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $result = (new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: null,
            shoppingTypes: null,
            shoppingIntent: null,
            shoppingContext: null,
            shoppingFor: 'for_household',
        ))->execute();

        $this->assertEquals('for_household', $result->moduleShopping->shopping_for);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'shopping_logged' && $job->user->id === $user->id;
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
    public function it_throws_when_journal_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Journal entry not found');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        (new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: null,
            shoppingTypes: null,
            shoppingIntent: null,
            shoppingContext: null,
            shoppingFor: 'for_self',
        ))->execute();
    }

    #[Test]
    public function it_throws_when_shopping_for_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        (new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: null,
            shoppingTypes: null,
            shoppingIntent: null,
            shoppingContext: null,
            shoppingFor: 'invalid',
        ))->execute();
    }
}
