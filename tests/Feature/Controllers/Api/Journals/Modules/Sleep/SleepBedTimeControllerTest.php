<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Journals\Modules\Sleep;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SleepBedTimeControllerTest extends TestCase
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
    public function it_logs_bedtime_for_a_journal_entry(): void
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

        $response = $this->json('PUT', '/api/journals/' . $journal->id . '/2025/4/12/sleep/bedtime', [
            'bedtime' => '22:30',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure($this->singleJsonStructure);
        $response->assertJson([
            'data' => [
                'id' => (string) $entry->id,
                'attributes' => [
                    'modules' => [
                        'sleep' => [
                            'bedtime' => '22:30',
                        ],
                    ],
                ],
            ],
        ]);

        $entry->refresh();
        $this->assertEquals('22:30', $entry->bedtime);
    }

    #[Test]
    public function it_requires_a_valid_bedtime_format(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('PUT', '/api/journals/' . $journal->id . '/2025/4/12/sleep/bedtime', [
            'bedtime' => '22-30',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('bedtime');
    }

    #[Test]
    public function it_rejects_bedtime_updates_for_other_users_entries(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('PUT', '/api/journals/' . $journal->id . '/2025/4/12/sleep/bedtime', [
            'bedtime' => '22:30',
        ]);

        $response->assertStatus(404);
    }
}
