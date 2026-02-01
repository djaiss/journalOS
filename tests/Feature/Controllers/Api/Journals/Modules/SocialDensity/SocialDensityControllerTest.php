<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Journals\Modules\SocialDensity;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SocialDensityControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_social_density_with_alone_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/social-density", [
            'social_density' => 'alone',
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
                        'mood',
                        'energy',
                        'social_density',
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
                        'social_density' => [
                            'social_density' => 'alone',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh();
        $this->assertEquals('alone', $entry->moduleSocialDensity?->social_density);
    }

    #[Test]
    public function it_updates_social_density_with_few_people_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/social-density", [
            'social_density' => 'few people',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'attributes' => [
                    'modules' => [
                        'social_density' => [
                            'social_density' => 'few people',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh();
        $this->assertEquals('few people', $entry->moduleSocialDensity?->social_density);
    }

    #[Test]
    public function it_updates_social_density_with_crowd_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/social-density", [
            'social_density' => 'crowd',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'attributes' => [
                    'modules' => [
                        'social_density' => [
                            'social_density' => 'crowd',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh();
        $this->assertEquals('crowd', $entry->moduleSocialDensity?->social_density);
    }

    #[Test]
    public function it_updates_social_density_with_too_much_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/social-density", [
            'social_density' => 'too much',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'attributes' => [
                    'modules' => [
                        'social_density' => [
                            'social_density' => 'too much',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh();
        $this->assertEquals('too much', $entry->moduleSocialDensity?->social_density);
    }

    #[Test]
    public function it_validates_social_density_must_be_valid(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/social-density", [
            'social_density' => 'invalid',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('social_density');
    }

    #[Test]
    public function it_validates_social_density_is_required(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/social-density", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('social_density');
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/social-density", [
            'social_density' => 'crowd',
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/social-density", [
            'social_density' => 'crowd',
        ]);

        $response->assertStatus(404);
    }
}
