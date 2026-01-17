<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogMeal;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LogMealTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_meal_details(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogMeal(
            user: $user,
            entry: $entry,
            breakfast: 'yes',
            lunch: 'no',
            dinner: 'yes',
            snack: 'no',
            mealType: 'home_cooked',
            socialContext: 'family',
            notes: 'Dinner with family.',
        )->execute();

        $this->assertEquals('yes', $entry->moduleMeal->breakfast);
        $this->assertEquals('no', $entry->moduleMeal->lunch);
        $this->assertEquals('yes', $entry->moduleMeal->dinner);
        $this->assertEquals('no', $entry->moduleMeal->snack);
        $this->assertEquals('home_cooked', $entry->moduleMeal->meal_type);
        $this->assertEquals('family', $entry->moduleMeal->social_context);
        $this->assertEquals('Dinner with family.', $entry->moduleMeal->notes);
    }

    #[Test]
    public function it_requires_at_least_one_value(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $this->expectException(ValidationException::class);

        new LogMeal(
            user: $user,
            entry: $entry,
            breakfast: null,
            lunch: null,
            dinner: null,
            snack: null,
            mealType: null,
            socialContext: null,
            notes: null,
        )->execute();
    }

    #[Test]
    public function it_rejects_invalid_meal_type(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $this->expectException(ValidationException::class);

        new LogMeal(
            user: $user,
            entry: $entry,
            breakfast: 'yes',
            lunch: null,
            dinner: null,
            snack: null,
            mealType: 'invalid',
            socialContext: null,
            notes: null,
        )->execute();
    }

    #[Test]
    public function it_requires_the_entry_to_belong_to_the_user(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $this->expectException(ModelNotFoundException::class);

        new LogMeal(
            user: $user,
            entry: $entry,
            breakfast: 'yes',
            lunch: null,
            dinner: null,
            snack: null,
            mealType: null,
            socialContext: null,
            notes: null,
        )->execute();
    }
}
