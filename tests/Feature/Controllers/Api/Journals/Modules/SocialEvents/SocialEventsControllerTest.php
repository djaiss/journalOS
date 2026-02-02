<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Journals\Modules\SocialEvents;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SocialEventsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_social_events_with_event_type_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/social-events", [
            'event_type' => 'friends',
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
                        'social_events',
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
                        'social_events' => [
                            'event_type' => 'friends',
                            'tone' => null,
                            'duration' => null,
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh();
        $this->assertEquals('friends', $entry->moduleSocialEvents?->event_type);
    }

    #[Test]
    public function it_updates_social_events_with_type_tone_duration_and_returns_journal_entry(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/social-events", [
            'event_type' => 'work',
            'tone' => 'neutral',
            'duration' => 'long',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'attributes' => [
                    'modules' => [
                        'social_events' => [
                            'event_type' => 'work',
                            'tone' => 'neutral',
                            'duration' => 'long',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh();
        $this->assertEquals('work', $entry->moduleSocialEvents?->event_type);
        $this->assertEquals('neutral', $entry->moduleSocialEvents?->tone);
        $this->assertEquals('long', $entry->moduleSocialEvents?->duration);
    }

    #[Test]
    public function it_validates_event_type_must_be_valid(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/social-events", [
            'event_type' => 'invalid',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('event_type');
    }

    #[Test]
    public function it_validates_tone_must_be_valid(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/social-events", [
            'tone' => 'invalid',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('tone');
    }

    #[Test]
    public function it_validates_duration_must_be_valid(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/social-events", [
            'duration' => 'invalid',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('duration');
    }

    #[Test]
    public function it_validates_social_events_are_required(): void
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/social-events", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['event_type', 'tone', 'duration']);
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/social-events", [
            'event_type' => 'friends',
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

        $response = $this->json('PUT', "/api/journals/{$journal->id}/2024/6/15/social-events", [
            'event_type' => 'friends',
        ]);

        $response->assertStatus(404);
    }
}
