<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Journals\Modules\PhysicalActivity;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModulePhysicalActivity;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PhysicalActivityControllerTest extends TestCase
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
                    'primary_obligation' => [
                        'primary_obligation',
                    ],
                    'physical_activity' => [
                        'has_done_physical_activity',
                        'activity_type',
                        'activity_intensity',
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
    public function it_updates_has_done_physical_activity_with_yes(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/physical-activity", [
            'has_done_physical_activity' => 'yes',
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure($this->singleJsonStructure);

        $entry->refresh();
        $this->assertEquals('yes', $entry->modulePhysicalActivity->has_done_physical_activity);
    }

    #[Test]
    public function it_updates_has_done_physical_activity_with_no(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/physical-activity", [
            'has_done_physical_activity' => 'no',
        ]);

        $response->assertStatus(200);

        $entry->refresh();
        $this->assertEquals('no', $entry->modulePhysicalActivity->has_done_physical_activity);
    }

    #[Test]
    public function it_updates_activity_type_with_running(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/physical-activity", [
            'activity_type' => 'running',
        ]);

        $response->assertStatus(200);

        $entry->refresh();
        $this->assertEquals('running', $entry->modulePhysicalActivity->activity_type);
    }

    #[Test]
    public function it_updates_activity_type_with_cycling(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/physical-activity", [
            'activity_type' => 'cycling',
        ]);

        $response->assertStatus(200);

        $entry->refresh();
        $this->assertEquals('cycling', $entry->modulePhysicalActivity->activity_type);
    }

    #[Test]
    public function it_updates_activity_type_with_swimming(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/physical-activity", [
            'activity_type' => 'swimming',
        ]);

        $response->assertStatus(200);

        $entry->refresh();
        $this->assertEquals('swimming', $entry->modulePhysicalActivity->activity_type);
    }

    #[Test]
    public function it_updates_activity_type_with_gym(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/physical-activity", [
            'activity_type' => 'gym',
        ]);

        $response->assertStatus(200);

        $entry->refresh();
        $this->assertEquals('gym', $entry->modulePhysicalActivity->activity_type);
    }

    #[Test]
    public function it_updates_activity_type_with_walking(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/physical-activity", [
            'activity_type' => 'walking',
        ]);

        $response->assertStatus(200);

        $entry->refresh();
        $this->assertEquals('walking', $entry->modulePhysicalActivity->activity_type);
    }

    #[Test]
    public function it_updates_activity_intensity_with_light(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/physical-activity", [
            'activity_intensity' => 'light',
        ]);

        $response->assertStatus(200);

        $entry->refresh();
        $this->assertEquals('light', $entry->modulePhysicalActivity->activity_intensity);
    }

    #[Test]
    public function it_updates_activity_intensity_with_moderate(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/physical-activity", [
            'activity_intensity' => 'moderate',
        ]);

        $response->assertStatus(200);

        $entry->refresh();
        $this->assertEquals('moderate', $entry->modulePhysicalActivity->activity_intensity);
    }

    #[Test]
    public function it_updates_activity_intensity_with_intense(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/physical-activity", [
            'activity_intensity' => 'intense',
        ]);

        $response->assertStatus(200);

        $entry->refresh();
        $this->assertEquals('intense', $entry->modulePhysicalActivity->activity_intensity);
    }

    #[Test]
    public function it_updates_all_three_fields_at_once(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/physical-activity", [
            'has_done_physical_activity' => 'yes',
            'activity_type' => 'swimming',
            'activity_intensity' => 'moderate',
        ]);

        $response->assertStatus(200);

        $entry->refresh();
        $this->assertEquals('yes', $entry->modulePhysicalActivity->has_done_physical_activity);
        $this->assertEquals('swimming', $entry->modulePhysicalActivity->activity_type);
        $this->assertEquals('moderate', $entry->modulePhysicalActivity->activity_intensity);
    }

    #[Test]
    public function it_ignores_nullable_fields_when_they_are_null(): void
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
        ModulePhysicalActivity::factory()->create([
            'journal_entry_id' => $entry->id,
            'has_done_physical_activity' => 'yes',
            'activity_type' => 'running',
            'activity_intensity' => 'light',
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/physical-activity", [
            'has_done_physical_activity' => null,
            'activity_type' => null,
            'activity_intensity' => null,
        ]);

        $response->assertStatus(200);

        $entry->refresh();
        $this->assertEquals('yes', $entry->modulePhysicalActivity->has_done_physical_activity);
        $this->assertEquals('running', $entry->modulePhysicalActivity->activity_type);
        $this->assertEquals('light', $entry->modulePhysicalActivity->activity_intensity);
    }

    #[Test]
    public function it_validates_has_done_physical_activity_must_be_valid(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/physical-activity", [
            'has_done_physical_activity' => 'invalid',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('has_done_physical_activity');
    }

    #[Test]
    public function it_validates_activity_type_must_be_valid(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/physical-activity", [
            'activity_type' => 'invalid',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('activity_type');
    }

    #[Test]
    public function it_validates_activity_intensity_must_be_valid(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/physical-activity", [
            'activity_intensity' => 'invalid',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('activity_intensity');
    }

    #[Test]
    public function unauthenticated_user_cannot_update_physical_activity(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/physical-activity", [
            'has_done_physical_activity' => 'yes',
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

        $response = $this->putJson("/api/journals/{$journal->id}/2024/6/15/physical-activity", [
            'has_done_physical_activity' => 'yes',
        ]);

        $response->assertStatus(404);
    }
}
