<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Journals\Modules\Travel;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class TravelModeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_travel_modes_with_single_mode(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/travel", [
            'travel_modes' => ['car'],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.attributes.modules.travel.travel_mode', ['car']);

        $entry->refresh();
        $entry->load('moduleTravel');
        $this->assertEquals(['car'], $entry->moduleTravel->travel_mode);
    }

    #[Test]
    public function it_updates_travel_modes_with_multiple_modes(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/travel", [
            'travel_modes' => ['car', 'train', 'plane'],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.attributes.modules.travel.travel_mode', ['car', 'train', 'plane']);

        $entry->refresh();
        $entry->load('moduleTravel');
        $this->assertEquals(['car', 'train', 'plane'], $entry->moduleTravel->travel_mode);
    }

    #[Test]
    public function it_updates_with_all_travel_modes(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $allModes = ['car', 'plane', 'train', 'bike', 'bus', 'walk', 'boat', 'other'];

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/travel", [
            'travel_modes' => $allModes,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.attributes.modules.travel.travel_mode', $allModes);

        $entry->refresh();
        $entry->load('moduleTravel');
        $this->assertEquals($allModes, $entry->moduleTravel->travel_mode);
    }

    #[Test]
    public function it_validates_travel_modes_must_be_array(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/travel", [
            'travel_modes' => 'car',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['travel_modes']);
    }

    #[Test]
    public function it_validates_travel_modes_cannot_be_empty(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/travel", [
            'travel_modes' => [],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['travel_modes']);
    }

    #[Test]
    public function it_validates_each_travel_mode_must_be_valid(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/travel", [
            'travel_modes' => ['car', 'invalid'],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['travel_modes.1']);
    }

    #[Test]
    public function it_validates_travel_modes_is_required(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/travel", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['travel_modes']);
    }

    #[Test]
    public function it_returns_401_for_unauthenticated_user(): void
    {
        $journal = Journal::factory()->create();
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/travel", [
            'travel_modes' => ['car'],
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_returns_404_for_unauthorized_entry(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $journal = Journal::factory()->create();
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/travel", [
            'travel_modes' => ['car'],
        ]);

        $response->assertStatus(404);
    }
}
