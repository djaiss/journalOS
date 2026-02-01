<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Journals\Modules\Shopping;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleShopping;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ShoppingResetControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resets_shopping_data_and_redirects(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);
        ModuleShopping::factory()->create([
            'journal_entry_id' => $entry->id,
            'has_shopped_today' => 'yes',
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2022/1/1/shopping/reset",
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2022/1/1");
        $response->assertSessionHas('status');

        $entry->refresh()->load('moduleShopping');
        $this->assertNull($entry->moduleShopping);
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $journal = Journal::factory()->create();
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->put("/journals/{$journal->slug}/entries/2022/1/1/shopping/reset");

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_returns_404_for_unauthorized_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);
        ModuleShopping::factory()->create([
            'journal_entry_id' => $entry->id,
            'has_shopped_today' => 'yes',
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2022/1/1/shopping/reset",
        );

        $response->assertNotFound();
    }
}
