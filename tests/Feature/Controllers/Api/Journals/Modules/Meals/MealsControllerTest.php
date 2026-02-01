<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Journals\Modules\Meals;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MealsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_meals_and_returns_journal_entry(): void
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

        Sanctum::actingAs($user);

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/meals", [
            'meal_presence' => ['breakfast', 'lunch'],
            'meal_type' => 'home_cooked',
            'social_context' => 'family',
            'has_notes' => 'yes',
            'notes' => 'Ate together.',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'type',
                'id',
                'attributes' => [
                    'journal_id',
                    'day',
                    'month',
                    'year',
                    'modules' => [
                        'sleep',
                        'work',
                        'travel',
                        'day_type',
                        'primary_obligation',
                        'physical_activity',
                        'health',
                        'meals',
                    ],
                    'created_at',
                    'updated_at',
                ],
                'links' => [
                    'self',
                ],
            ],
        ]);
        $response->assertJson([
            'data' => [
                'attributes' => [
                    'modules' => [
                        'meals' => [
                            'meal_presence' => ['breakfast', 'lunch'],
                            'meal_type' => 'home_cooked',
                            'social_context' => 'family',
                            'has_notes' => 'yes',
                            'notes' => 'Ate together.',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh()->load('moduleMeals');
        $this->assertEquals(['breakfast', 'lunch'], $entry->moduleMeals->meal_presence);
        $this->assertEquals('home_cooked', $entry->moduleMeals->meal_type);
        $this->assertEquals('family', $entry->moduleMeals->social_context);
        $this->assertEquals('yes', $entry->moduleMeals->has_notes);
        $this->assertEquals('Ate together.', $entry->moduleMeals->notes);
    }

    #[Test]
    public function it_validates_meals_payload_is_required(): void
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

        Sanctum::actingAs($user);

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/meals", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['meal_presence', 'meal_type', 'social_context', 'has_notes', 'notes']);
    }
}
