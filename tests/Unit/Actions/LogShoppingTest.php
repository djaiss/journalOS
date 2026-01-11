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

final class LogShoppingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_logs_has_shopped_yes(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: 'yes',
            shoppingTypes: null,
            shoppingIntent: null,
            shoppingContext: null,
            shoppingFor: null,
        )->execute();

        $this->assertEquals('yes', $entry->moduleShopping->has_shopped_today);

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
    public function it_logs_has_shopped_no(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: 'no',
            shoppingTypes: null,
            shoppingIntent: null,
            shoppingContext: null,
            shoppingFor: null,
        )->execute();

        $this->assertEquals('no', $entry->moduleShopping->has_shopped_today);
    }

    #[Test]
    public function it_logs_shopping_types_single(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: null,
            shoppingTypes: ['groceries'],
            shoppingIntent: null,
            shoppingContext: null,
            shoppingFor: null,
        )->execute();

        $this->assertEquals(['groceries'], $entry->moduleShopping->shopping_type);
    }

    #[Test]
    public function it_logs_shopping_types_multiple(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: null,
            shoppingTypes: ['groceries', 'clothes', 'electronics_tech'],
            shoppingIntent: null,
            shoppingContext: null,
            shoppingFor: null,
        )->execute();

        $this->assertEquals(['groceries', 'clothes', 'electronics_tech'], $entry->moduleShopping->shopping_type);
    }

    #[Test]
    public function it_logs_shopping_intent(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: null,
            shoppingTypes: null,
            shoppingIntent: 'planned',
            shoppingContext: null,
            shoppingFor: null,
        )->execute();

        $this->assertEquals('planned', $entry->moduleShopping->shopping_intent);
    }

    #[Test]
    public function it_logs_shopping_context(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: null,
            shoppingTypes: null,
            shoppingIntent: null,
            shoppingContext: 'alone',
            shoppingFor: null,
        )->execute();

        $this->assertEquals('alone', $entry->moduleShopping->shopping_context);
    }

    #[Test]
    public function it_logs_shopping_for(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: null,
            shoppingTypes: null,
            shoppingIntent: null,
            shoppingContext: null,
            shoppingFor: 'for_self',
        )->execute();

        $this->assertEquals('for_self', $entry->moduleShopping->shopping_for);
    }

    #[Test]
    public function it_logs_all_fields(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: 'yes',
            shoppingTypes: ['groceries', 'clothes'],
            shoppingIntent: 'planned',
            shoppingContext: 'with_partner',
            shoppingFor: 'for_household',
        )->execute();

        $this->assertEquals('yes', $entry->moduleShopping->has_shopped_today);
        $this->assertEquals(['groceries', 'clothes'], $entry->moduleShopping->shopping_type);
        $this->assertEquals('planned', $entry->moduleShopping->shopping_intent);
        $this->assertEquals('with_partner', $entry->moduleShopping->shopping_context);
        $this->assertEquals('for_household', $entry->moduleShopping->shopping_for);
    }

    #[Test]
    public function it_throws_when_entry_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: 'yes',
            shoppingTypes: null,
            shoppingIntent: null,
            shoppingContext: null,
            shoppingFor: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_all_values_are_null(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: null,
            shoppingTypes: null,
            shoppingIntent: null,
            shoppingContext: null,
            shoppingFor: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_has_shopped_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: 'invalid',
            shoppingTypes: null,
            shoppingIntent: null,
            shoppingContext: null,
            shoppingFor: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_shopping_types_is_empty(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: null,
            shoppingTypes: [],
            shoppingIntent: null,
            shoppingContext: null,
            shoppingFor: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_shopping_type_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: null,
            shoppingTypes: ['invalid'],
            shoppingIntent: null,
            shoppingContext: null,
            shoppingFor: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_shopping_intent_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: null,
            shoppingTypes: null,
            shoppingIntent: 'invalid',
            shoppingContext: null,
            shoppingFor: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_shopping_context_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: null,
            shoppingTypes: null,
            shoppingIntent: null,
            shoppingContext: 'invalid',
            shoppingFor: null,
        )->execute();
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

        new LogShopping(
            user: $user,
            entry: $entry,
            hasShopped: null,
            shoppingTypes: null,
            shoppingIntent: null,
            shoppingContext: null,
            shoppingFor: 'invalid',
        )->execute();
    }
}
