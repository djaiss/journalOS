<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Journals\Modules\DayType;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class DayTypeControllerTest extends TestCase
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
                        'work_load',
                        'work_procrastinated',
                    ],
                    'travel' => [
                        'has_traveled_today',
                        'travel_mode',
                    ],
                    'day_type' => [
                        'day_type',
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
    public function it_updates_day_type_with_workday(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'day_type' => null,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/day-type", [
            'day_type' => 'workday',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure($this->singleJsonStructure);

        $entry->refresh();
        $this->assertEquals('workday', $entry->day_type);
    }

    #[Test]
    public function it_updates_day_type_with_day_off(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'day_type' => null,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/day-type", [
            'day_type' => 'day off',
        ]);

        $response->assertStatus(200);

        $entry->refresh();
        $this->assertEquals('day off', $entry->day_type);
    }

    #[Test]
    public function it_updates_day_type_with_weekend(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'day_type' => null,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/day-type", [
            'day_type' => 'weekend',
        ]);

        $response->assertStatus(200);

        $entry->refresh();
        $this->assertEquals('weekend', $entry->day_type);
    }

    #[Test]
    public function it_updates_day_type_with_vacation(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'day_type' => null,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/day-type", [
            'day_type' => 'vacation',
        ]);

        $response->assertStatus(200);

        $entry->refresh();
        $this->assertEquals('vacation', $entry->day_type);
    }

    #[Test]
    public function it_updates_day_type_with_sick_day(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'day_type' => null,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/day-type", [
            'day_type' => 'sick day',
        ]);

        $response->assertStatus(200);

        $entry->refresh();
        $this->assertEquals('sick day', $entry->day_type);
    }

    #[Test]
    public function it_validates_day_type_is_required(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/day-type", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('day_type');
    }

    #[Test]
    public function it_validates_day_type_must_be_valid(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/day-type", [
            'day_type' => 'invalid',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('day_type');
    }

    #[Test]
    public function unauthenticated_user_cannot_update_day_type(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/day-type", [
            'day_type' => 'workday',
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function user_cannot_update_another_users_journal_entry(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $otherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/day-type", [
            'day_type' => 'workday',
        ]);

        $response->assertStatus(404);
    }
}
