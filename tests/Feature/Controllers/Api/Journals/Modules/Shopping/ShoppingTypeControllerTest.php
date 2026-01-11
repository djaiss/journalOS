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

final class ShoppingTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_shopping_types_with_single_type(): void
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
            'shopping_types' => ['groceries'],
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.attributes.modules.shopping.shopping_type', ['groceries']);

        $entry->refresh();
        $entry->load('moduleShopping');
        $this->assertEquals(['groceries'], $entry->moduleShopping->shopping_type);
    }

    #[Test]
    public function it_updates_shopping_types_with_multiple_types(): void
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

        $types = ['groceries', 'books_media', 'gifts'];

        $response = $this->putJson("/api/journals/{$journal->id}/2022/1/1/shopping", [
            'shopping_types' => $types,
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.attributes.modules.shopping.shopping_type', $types);

        $entry->refresh();
        $entry->load('moduleShopping');
        $this->assertEquals($types, $entry->moduleShopping->shopping_type);
    }

    #[Test]
    public function it_validates_shopping_types_must_be_array(): void
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
            'shopping_types' => 'groceries',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['shopping_types']);
    }

    #[Test]
    public function it_validates_each_shopping_type_must_be_valid(): void
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
            'shopping_types' => ['groceries', 'invalid'],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['shopping_types.1']);
    }

    #[Test]
    public function it_validates_shopping_types_is_required(): void
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
        $response->assertJsonValidationErrors(['shopping_types']);
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
            'shopping_types' => ['groceries'],
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
            'shopping_types' => ['groceries'],
        ]);

        $response->assertStatus(404);
    }
}
