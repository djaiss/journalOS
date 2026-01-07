<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Journals\Modules\Mood;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MoodControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_mood_with_terrible_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/mood", [
            'mood' => 'terrible',
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
                        'mood' => [
                            'mood' => 'terrible',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh();
        $this->assertEquals('terrible', $entry->moduleMood->mood);
    }

    #[Test]
    public function it_updates_mood_with_bad_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/mood", [
            'mood' => 'bad',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'attributes' => [
                    'modules' => [
                        'mood' => [
                            'mood' => 'bad',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh();
        $this->assertEquals('bad', $entry->moduleMood->mood);
    }

    #[Test]
    public function it_updates_mood_with_okay_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/mood", [
            'mood' => 'okay',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'attributes' => [
                    'modules' => [
                        'mood' => [
                            'mood' => 'okay',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh();
        $this->assertEquals('okay', $entry->moduleMood->mood);
    }

    #[Test]
    public function it_updates_mood_with_good_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/mood", [
            'mood' => 'good',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'attributes' => [
                    'modules' => [
                        'mood' => [
                            'mood' => 'good',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh();
        $this->assertEquals('good', $entry->moduleMood->mood);
    }

    #[Test]
    public function it_updates_mood_with_great_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/mood", [
            'mood' => 'great',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'attributes' => [
                    'modules' => [
                        'mood' => [
                            'mood' => 'great',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh();
        $this->assertEquals('great', $entry->moduleMood->mood);
    }

    #[Test]
    public function it_validates_mood_must_be_valid(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/mood", [
            'mood' => 'invalid',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('mood');
    }

    #[Test]
    public function it_validates_mood_is_required(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/mood", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('mood');
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/mood", [
            'mood' => 'good',
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/mood", [
            'mood' => 'good',
        ]);

        $response->assertStatus(404);
    }
}
