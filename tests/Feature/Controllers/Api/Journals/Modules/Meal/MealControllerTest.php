<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Journals\Modules\Meal;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MealControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_meal_details(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/meal", [
            'breakfast' => 'yes',
            'meal_type' => 'restaurant',
            'social_context' => 'friends',
            'notes' => 'Quick brunch.',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.attributes.modules.meal.breakfast', 'yes');
        $response->assertJsonPath('data.attributes.modules.meal.meal_type', 'restaurant');
        $response->assertJsonPath('data.attributes.modules.meal.social_context', 'friends');
        $response->assertJsonPath('data.attributes.modules.meal.notes', 'Quick brunch.');

        $entry->refresh();
        $entry->load('moduleMeal');
        $this->assertEquals('yes', $entry->moduleMeal->breakfast);
    }

    #[Test]
    public function it_validates_meal_type_must_be_valid(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/meal", [
            'meal_type' => 'invalid',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['meal_type']);
    }

    #[Test]
    public function it_validates_meal_requires_at_least_one_value(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/meal", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['breakfast']);
    }

    #[Test]
    public function it_returns_401_for_unauthenticated_user(): void
    {
        $journal = Journal::factory()->create();
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/meal", [
            'breakfast' => 'yes',
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_returns_404_for_unauthorized_entry(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create();
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/meal", [
            'breakfast' => 'yes',
        ]);

        $response->assertStatus(404);
    }
}
