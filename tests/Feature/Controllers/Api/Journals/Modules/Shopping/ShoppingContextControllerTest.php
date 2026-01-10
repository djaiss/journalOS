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

final class ShoppingContextControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_shopping_context_with_alone(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/shopping/context", [
            'shopping_context' => 'alone',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.attributes.modules.shopping.shopping_context', 'alone');

        $entry->refresh();
        $entry->load('moduleShopping');
        $this->assertEquals('alone', $entry->moduleShopping->shopping_context);
    }

    #[Test]
    public function it_updates_shopping_context_with_with_kids(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/shopping/context", [
            'shopping_context' => 'with_kids',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.attributes.modules.shopping.shopping_context', 'with_kids');

        $entry->refresh();
        $entry->load('moduleShopping');
        $this->assertEquals('with_kids', $entry->moduleShopping->shopping_context);
    }

    #[Test]
    public function it_validates_shopping_context_must_be_valid(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/shopping/context", [
            'shopping_context' => 'invalid',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['shopping_context']);
    }

    #[Test]
    public function it_validates_shopping_context_is_required(): void
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

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/shopping/context", []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['shopping_context']);
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

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/shopping/context", [
            'shopping_context' => 'alone',
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

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/shopping/context", [
            'shopping_context' => 'alone',
        ]);

        $response->assertStatus(404);
    }
}
