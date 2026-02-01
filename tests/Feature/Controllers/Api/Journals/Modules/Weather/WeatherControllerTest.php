<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Journals\Modules\Weather;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class WeatherControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_weather_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/weather", [
            'condition' => 'snow',
            'temperature_range' => 'very_cold',
            'precipitation' => 'heavy',
            'daylight' => 'very_short',
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
                        'weather',
                        'day_type',
                        'primary_obligation',
                        'physical_activity',
                        'health',
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
                        'weather' => [
                            'condition' => 'snow',
                            'temperature_range' => 'very_cold',
                            'precipitation' => 'heavy',
                            'daylight' => 'very_short',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh()->load('moduleWeather');
        $this->assertEquals('snow', $entry->moduleWeather->condition);
        $this->assertEquals('very_cold', $entry->moduleWeather->temperature_range);
        $this->assertEquals('heavy', $entry->moduleWeather->precipitation);
        $this->assertEquals('very_short', $entry->moduleWeather->daylight);
    }

    #[Test]
    public function it_validates_condition_must_be_valid(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/weather", [
            'condition' => 'stormy',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('condition');
    }

    #[Test]
    public function it_validates_condition_is_required(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/weather", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('condition');
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/weather", [
            'condition' => 'sunny',
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_returns_404_for_unauthorized_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/weather", [
            'condition' => 'sunny',
        ]);

        $response->assertStatus(404);
    }
}
