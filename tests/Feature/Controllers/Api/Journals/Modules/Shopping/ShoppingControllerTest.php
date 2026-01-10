<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Journals\Modules\Shopping;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ShoppingControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_has_shopped_with_yes(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/shopping", [
            'has_shopped' => 'yes',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.attributes.modules.shopping.has_shopped_today', 'yes');

        $entry->refresh();
        $entry->load('moduleShopping');
        $this->assertEquals('yes', $entry->moduleShopping->has_shopped_today);
    }

    #[Test]
    public function it_updates_has_shopped_with_no(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/shopping", [
            'has_shopped' => 'no',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.attributes.modules.shopping.has_shopped_today', 'no');

        $entry->refresh();
        $entry->load('moduleShopping');
        $this->assertEquals('no', $entry->moduleShopping->has_shopped_today);
    }

    #[Test]
    public function it_validates_has_shopped_must_be_yes_or_no(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/shopping", [
            'has_shopped' => 'maybe',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['has_shopped']);
    }

    #[Test]
    public function it_validates_has_shopped_is_required(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/shopping", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['has_shopped']);
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

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/shopping", [
            'has_shopped' => 'yes',
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

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/shopping", [
            'has_shopped' => 'yes',
        ]);

        $response->assertStatus(404);
    }
}
