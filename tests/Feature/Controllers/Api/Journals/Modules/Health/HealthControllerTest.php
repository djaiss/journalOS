<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Journals\Modules\Health;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class HealthControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_health_with_good_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/health", [
            'health' => 'good',
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
                        'health' => [
                            'health' => 'good',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh()->load('moduleHealth');
        $this->assertEquals('good', $entry->moduleHealth->health);
    }

    #[Test]
    public function it_updates_health_with_okay_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/health", [
            'health' => 'okay',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'attributes' => [
                    'modules' => [
                        'health' => [
                            'health' => 'okay',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh()->load('moduleHealth');
        $this->assertEquals('okay', $entry->moduleHealth->health);
    }

    #[Test]
    public function it_updates_health_with_not_great_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/health", [
            'health' => 'not great',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'attributes' => [
                    'modules' => [
                        'health' => [
                            'health' => 'not great',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh()->load('moduleHealth');
        $this->assertEquals('not great', $entry->moduleHealth->health);
    }

    #[Test]
    public function it_validates_health_must_be_valid(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/health", [
            'health' => 'invalid',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('health');
    }

    #[Test]
    public function it_validates_health_is_required(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/health", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('health');
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/health", [
            'health' => 'good',
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/health", [
            'health' => 'good',
        ]);

        $response->assertStatus(404);
    }
}
