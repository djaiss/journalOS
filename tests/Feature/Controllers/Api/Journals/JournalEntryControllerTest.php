<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Journals;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalEntryControllerTest extends TestCase
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
    public function it_can_show_a_journal_entry_for_a_specific_day(): void
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
            'bedtime' => '22:30',
            'wake_up_time' => '06:45',
            'sleep_duration_in_minutes' => '495',
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/journals/' . $journal->id . '/2025/4/12');

        $response->assertStatus(200);
        $response->assertJsonStructure($this->singleJsonStructure);
        $response->assertJson([
            'data' => [
                'type' => 'journal_entry',
                'id' => (string) $entry->id,
                'attributes' => [
                    'journal_id' => $journal->id,
                    'day' => 12,
                    'month' => 4,
                    'year' => 2025,
                    'modules' => [
                        'sleep' => [
                            'bedtime' => '22:30',
                            'wake_up_time' => '06:45',
                            'sleep_duration_in_minutes' => '495',
                        ],
                    ],
                ],
            ],
        ]);
    }

    #[Test]
    public function it_returns_not_found_for_invalid_dates(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/journals/' . $journal->id . '/2025/2/31');

        $response->assertStatus(404);
    }

    #[Test]
    public function it_restricts_access_to_journal_entries_the_user_does_not_own(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/journals/' . $journal->id . '/2025/4/12');

        $response->assertStatus(404);
    }
}
