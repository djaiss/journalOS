<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\LogMeals;
use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleMeals;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LogMealsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_logs_meal_presence(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogMeals(
            user: $user,
            entry: $entry,
            mealPresence: ['breakfast', 'dinner'],
            mealType: null,
            socialContext: null,
            hasNotes: null,
            notes: null,
        )->execute();

        $this->assertEquals(['breakfast', 'dinner'], $entry->moduleMeals->meal_presence);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'meals_logged' && $job->user->id === $user->id;
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
    public function it_logs_meal_type(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogMeals(
            user: $user,
            entry: $entry,
            mealPresence: null,
            mealType: 'home_cooked',
            socialContext: null,
            hasNotes: null,
            notes: null,
        )->execute();

        $this->assertEquals('home_cooked', $entry->moduleMeals->meal_type);
    }

    #[Test]
    public function it_logs_social_context(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogMeals(
            user: $user,
            entry: $entry,
            mealPresence: null,
            mealType: null,
            socialContext: 'family',
            hasNotes: null,
            notes: null,
        )->execute();

        $this->assertEquals('family', $entry->moduleMeals->social_context);
    }

    #[Test]
    public function it_logs_notes_when_enabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogMeals(
            user: $user,
            entry: $entry,
            mealPresence: null,
            mealType: null,
            socialContext: null,
            hasNotes: 'yes',
            notes: 'Shared a new recipe.',
        )->execute();

        $this->assertEquals('yes', $entry->moduleMeals->has_notes);
        $this->assertEquals('Shared a new recipe.', $entry->moduleMeals->notes);
    }

    #[Test]
    public function it_clears_notes_when_disabled(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleMeals::factory()->create([
            'journal_entry_id' => $entry->id,
            'has_notes' => 'yes',
            'notes' => 'Already written.',
        ]);

        $entry = new LogMeals(
            user: $user,
            entry: $entry,
            mealPresence: null,
            mealType: null,
            socialContext: null,
            hasNotes: 'no',
            notes: null,
        )->execute();

        $this->assertEquals('no', $entry->moduleMeals->has_notes);
        $this->assertNull($entry->moduleMeals->notes);
    }

    #[Test]
    public function it_requires_at_least_one_value(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogMeals(
            user: $user,
            entry: $entry,
            mealPresence: null,
            mealType: null,
            socialContext: null,
            hasNotes: null,
            notes: null,
        )->execute();
    }

    #[Test]
    public function it_requires_valid_meal_presence_values(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogMeals(
            user: $user,
            entry: $entry,
            mealPresence: ['brunch'],
            mealType: null,
            socialContext: null,
            hasNotes: null,
            notes: null,
        )->execute();
    }

    #[Test]
    public function it_requires_valid_meal_type(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogMeals(
            user: $user,
            entry: $entry,
            mealPresence: null,
            mealType: 'drive_through',
            socialContext: null,
            hasNotes: null,
            notes: null,
        )->execute();
    }

    #[Test]
    public function it_requires_valid_social_context(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogMeals(
            user: $user,
            entry: $entry,
            mealPresence: null,
            mealType: null,
            socialContext: 'neighbors',
            hasNotes: null,
            notes: null,
        )->execute();
    }

    #[Test]
    public function it_requires_notes_to_be_disabled_without_content(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogMeals(
            user: $user,
            entry: $entry,
            mealPresence: null,
            mealType: null,
            socialContext: null,
            hasNotes: 'no',
            notes: 'Should not keep this.',
        )->execute();
    }

    #[Test]
    public function it_blocks_logging_for_unowned_entries(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $entry = JournalEntry::factory()->create();

        new LogMeals(
            user: $user,
            entry: $entry,
            mealPresence: ['breakfast'],
            mealType: null,
            socialContext: null,
            hasNotes: null,
            notes: null,
        )->execute();
    }
}
