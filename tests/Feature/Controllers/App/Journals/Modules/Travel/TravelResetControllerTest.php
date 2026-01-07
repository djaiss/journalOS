<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Modules\Travel;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleTravel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class TravelResetControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resets_travel_data_and_redirects(): void
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
        ModuleTravel::factory()->create([
            'journal_entry_id' => $entry->id,
            'has_traveled_today' => 'yes',
            'travel_mode' => ['car', 'train'],
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2022/1/1/travel/reset",
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2022/1/1");
        $response->assertSessionHas('status');

        $entry->refresh();
        $entry->load('moduleTravel');
        $this->assertNull($entry->moduleTravel);
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
        ]);

        $response = $this->put("/journals/{$journal->slug}/entries/2022/1/1/travel/reset");

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
        ModuleTravel::factory()->create([
            'journal_entry_id' => $entry->id,
            'has_traveled_today' => 'yes',
            'travel_mode' => ['plane', 'bus'],
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2022/1/1/travel/reset",
        );

        $response->assertStatus(404);

        $entry->refresh();
        $entry->load('moduleTravel');
        $this->assertNotNull($entry->moduleTravel);
    }
}
