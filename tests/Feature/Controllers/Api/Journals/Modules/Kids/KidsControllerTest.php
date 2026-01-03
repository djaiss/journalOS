<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Journals\Modules\Kids;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class KidsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_kids_today_with_yes(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/kids", [
            'had_kids_today' => 'yes',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.attributes.modules.kids.had_kids_today', 'yes');

        $entry->refresh();
        $this->assertEquals('yes', $entry->had_kids_today);
    }

    #[Test]
    public function it_updates_kids_today_with_no(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/kids", [
            'had_kids_today' => 'no',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.attributes.modules.kids.had_kids_today', 'no');

        $entry->refresh();
        $this->assertEquals('no', $entry->had_kids_today);
    }

    #[Test]
    public function it_validates_kids_today_must_be_yes_or_no(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/kids", [
            'had_kids_today' => 'maybe',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['had_kids_today']);
    }

    #[Test]
    public function it_validates_kids_today_is_required(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/kids", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['had_kids_today']);
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

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/kids", [
            'had_kids_today' => 'yes',
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

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/kids", [
            'had_kids_today' => 'yes',
        ]);

        $response->assertStatus(404);
    }
}
