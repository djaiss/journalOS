<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Journals\Modules\Shopping;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ShoppingForControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_shopping_for_with_self(): void
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
            'shopping_for' => 'for_self',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.attributes.modules.shopping.shopping_for', 'for_self');

        $entry->refresh();
        $entry->load('moduleShopping');
        $this->assertEquals('for_self', $entry->moduleShopping->shopping_for);
    }

    #[Test]
    public function it_updates_shopping_for_with_others(): void
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
            'shopping_for' => 'for_others',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.attributes.modules.shopping.shopping_for', 'for_others');

        $entry->refresh();
        $entry->load('moduleShopping');
        $this->assertEquals('for_others', $entry->moduleShopping->shopping_for);
    }

    #[Test]
    public function it_validates_shopping_for_must_be_valid(): void
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
            'shopping_for' => 'invalid',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['shopping_for']);
    }

    #[Test]
    public function it_validates_shopping_for_is_required(): void
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
        $response->assertJsonValidationErrors(['shopping_for']);
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
            'shopping_for' => 'for_self',
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
            'shopping_for' => 'for_self',
        ]);

        $response->assertStatus(404);
    }
}
