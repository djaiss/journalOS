<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogShoppingType;
use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LogShoppingTypeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_single_shopping_type(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $result = (new LogShoppingType(
            user: $user,
            entry: $entry,
            shoppingTypes: ['groceries'],
        ))->execute();

        $this->assertEquals(['groceries'], $result->moduleShopping->shopping_type);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'shopping_type_logged' && $job->user->id === $user->id;
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
    public function it_logs_multiple_shopping_types(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $result = (new LogShoppingType(
            user: $user,
            entry: $entry,
            shoppingTypes: ['groceries', 'books_media', 'gifts'],
        ))->execute();

        $this->assertEquals(['groceries', 'books_media', 'gifts'], $result->moduleShopping->shopping_type);
    }

    #[Test]
    public function it_throws_when_journal_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Journal not found');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        (new LogShoppingType(
            user: $user,
            entry: $entry,
            shoppingTypes: ['groceries'],
        ))->execute();
    }

    #[Test]
    public function it_throws_when_shopping_types_are_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('shoppingTypes cannot be empty');

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        (new LogShoppingType(
            user: $user,
            entry: $entry,
            shoppingTypes: [],
        ))->execute();
    }

    #[Test]
    public function it_throws_when_shopping_type_is_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Each shoppingType must be one of: groceries, clothes, electronics_tech, household_essentials, books_media, gifts, online_shopping, other');

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        (new LogShoppingType(
            user: $user,
            entry: $entry,
            shoppingTypes: ['invalid'],
        ))->execute();
    }
}
