<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Journals\Modules\WeatherInfluence;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class WeatherInfluenceControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_weather_influence_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/weather-influence", [
            'mood_effect' => 'negative',
            'energy_effect' => 'drained',
            'plans_influence' => 'significant',
            'outside_time' => 'barely',
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
                        'weather_influence',
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
                        'weather_influence' => [
                            'mood_effect' => 'negative',
                            'energy_effect' => 'drained',
                            'plans_influence' => 'significant',
                            'outside_time' => 'barely',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh()->load('moduleWeatherInfluence');
        $this->assertEquals('negative', $entry->moduleWeatherInfluence->mood_effect);
        $this->assertEquals('drained', $entry->moduleWeatherInfluence->energy_effect);
        $this->assertEquals('significant', $entry->moduleWeatherInfluence->plans_influence);
        $this->assertEquals('barely', $entry->moduleWeatherInfluence->outside_time);
    }

    #[Test]
    public function it_validates_mood_effect_must_be_valid(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/weather-influence", [
            'mood_effect' => 'extreme',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('mood_effect');
    }

    #[Test]
    public function it_validates_mood_effect_is_required(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/weather-influence", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('mood_effect');
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/weather-influence", [
            'mood_effect' => 'positive',
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/weather-influence", [
            'mood_effect' => 'positive',
        ]);

        $response->assertStatus(404);
    }
}
