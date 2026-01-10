<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Modules\Shopping;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ShoppingTypeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_shopping_types_with_single_type(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/shopping/type",
            ['shopping_types' => ['groceries']],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh();
        $entry->load('moduleShopping');
        $this->assertEquals(['groceries'], $entry->moduleShopping->shopping_type);
    }

    #[Test]
    public function it_updates_shopping_types_with_multiple_types(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/shopping/type",
            ['shopping_types' => ['groceries', 'books_media', 'gifts']],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh();
        $entry->load('moduleShopping');
        $this->assertEquals(['groceries', 'books_media', 'gifts'], $entry->moduleShopping->shopping_type);
        $this->assertContains('groceries', $entry->moduleShopping->shopping_type);
        $this->assertContains('books_media', $entry->moduleShopping->shopping_type);
        $this->assertContains('gifts', $entry->moduleShopping->shopping_type);
    }

    #[Test]
    public function it_updates_with_all_shopping_types(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $allTypes = ['groceries', 'clothes', 'electronics_tech', 'household_essentials', 'books_media', 'gifts', 'online_shopping', 'other'];

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/shopping/type",
            ['shopping_types' => $allTypes],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh();
        $entry->load('moduleShopping');
        $this->assertEquals($allTypes, $entry->moduleShopping->shopping_type);
        $this->assertCount(8, $entry->moduleShopping->shopping_type);
    }

    #[Test]
    public function it_validates_shopping_types_must_be_array(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/shopping/type",
            ['shopping_types' => 'groceries'],
        );

        $response->assertSessionHasErrors('shopping_types');
    }

    #[Test]
    public function it_validates_shopping_types_cannot_be_empty(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/shopping/type",
            ['shopping_types' => []],
        );

        $response->assertSessionHasErrors('shopping_types');
    }

    #[Test]
    public function it_validates_each_shopping_type_must_be_valid(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/shopping/type",
            ['shopping_types' => ['groceries', 'invalid', 'gifts']],
        );

        $response->assertSessionHasErrors('shopping_types.1');
    }

    #[Test]
    public function it_validates_shopping_types_is_required(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/shopping/type",
            [],
        );

        $response->assertSessionHasErrors('shopping_types');
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $journal = Journal::factory()->create();
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->put(
            "/journals/{$journal->slug}/entries/2024/6/15/shopping/type",
            ['shopping_types' => ['groceries']],
        );

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_returns_404_for_unauthorized_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/shopping/type",
            ['shopping_types' => ['groceries']],
        );

        $response->assertNotFound();
    }
}
