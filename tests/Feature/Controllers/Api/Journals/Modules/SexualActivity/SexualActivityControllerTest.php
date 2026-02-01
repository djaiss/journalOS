<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Journals\Modules\SexualActivity;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SexualActivityControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $singleJsonStructure = [
        'data' => [
            'type',
            'id',
            'attributes' => [
                'journal_id',
                'day',
                'month',
                'year',
                'modules' => [
                    'sleep' => [
                        'bedtime',
                        'wake_up_time',
                        'sleep_duration_in_minutes',
                    ],
                    'work' => [
                        'worked',
                        'work_mode',
                    ],
                    'primary_obligation' => [
                        'primary_obligation',
                    ],
                ],
                'created_at',
                'updated_at',
            ],
            'links' => [
                'self',
            ],
        ],
    ];

    #[Test]
    public function it_logs_sexual_activity_for_a_journal_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'day' => 12,
            'month' => 4,
            'year' => 2025,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('PUT', '/api/journals/' . $journal->id . '/2025/4/12/sexual-activity', [
            'had_sexual_activity' => 'yes',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure($this->singleJsonStructure);
        $response->assertJson([
            'data' => [
                'id' => (string) $entry->id,
            ],
        ]);

        $entry->refresh();
        $this->assertEquals('yes', $entry->moduleSexualActivity->had_sexual_activity);
    }

    #[Test]
    public function it_requires_had_sexual_activity_to_be_yes_or_no(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('PUT', '/api/journals/' . $journal->id . '/2025/4/12/sexual-activity', [
            'had_sexual_activity' => 'maybe',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('had_sexual_activity');
    }

    #[Test]
    public function it_requires_had_sexual_activity_to_be_present(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('PUT', '/api/journals/' . $journal->id . '/2025/4/12/sexual-activity', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('had_sexual_activity');
    }

    #[Test]
    public function it_rejects_sexual_activity_updates_for_other_users_entries(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('PUT', '/api/journals/' . $journal->id . '/2025/4/12/sexual-activity', [
            'had_sexual_activity' => 'yes',
        ]);

        $response->assertStatus(404);
    }
}
