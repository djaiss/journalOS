<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Modules\Energy;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class EnergyResetControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resets_energy_data_and_redirects(): void
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
            'energy' => 'high',
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2022/1/1/energy/reset",
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2022/1/1");
        $response->assertSessionHas('status');

        $entry->refresh();
        $this->assertNull($entry->energy);
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

        $response = $this->put("/journals/{$journal->slug}/entries/2022/1/1/energy/reset");

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
            'energy' => 'low',
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2022/1/1/energy/reset",
        );

        $response->assertNotFound();
    }
}
