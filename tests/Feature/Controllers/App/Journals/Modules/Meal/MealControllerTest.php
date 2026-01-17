<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Modules\Meal;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MealControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_meal_details_and_redirects(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/meal",
            [
                'breakfast' => 'yes',
                'lunch' => 'no',
                'meal_type' => 'home_cooked',
                'social_context' => 'family',
                'notes' => 'Simple lunch.',
            ],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh();
        $this->assertEquals('yes', $entry->moduleMeal->breakfast);
        $this->assertEquals('no', $entry->moduleMeal->lunch);
        $this->assertEquals('home_cooked', $entry->moduleMeal->meal_type);
        $this->assertEquals('family', $entry->moduleMeal->social_context);
        $this->assertEquals('Simple lunch.', $entry->moduleMeal->notes);
    }

    #[Test]
    public function it_validates_meal_type_must_be_valid(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/meal",
            ['meal_type' => 'invalid'],
        );

        $response->assertSessionHasErrors('meal_type');
    }

    #[Test]
    public function it_validates_meal_requires_at_least_one_value(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/meal",
            [],
        );

        $response->assertSessionHasErrors('breakfast');
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $journal = Journal::factory()->create();
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->put("/journals/{$journal->slug}/entries/2024/6/15/meal", [
            'breakfast' => 'yes',
        ]);

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_returns_404_for_unauthorized_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/meal",
            ['breakfast' => 'yes'],
        );

        $response->assertNotFound();
    }
}
