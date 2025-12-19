<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class JournalEntryControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_a_journal_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'day' => 15,
            'month' => 12,
            'year' => 2025,
        ]);

        $response = $this->actingAs($user)->get(
            "/journals/{$journal->slug}/entries/{$entry->year}/{$entry->month}/{$entry->day}",
        );

        $response->assertStatus(200);
        // Optionally assert view data or content
    }

    #[Test]
    public function it_returns_404_for_unauthorized_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'day' => 15,
            'month' => 12,
            'year' => 2025,
        ]);

        $response = $this->actingAs($user)->get(
            "/journals/{$journal->slug}/entries/{$entry->year}/{$entry->month}/{$entry->day}",
        );

        $response->assertStatus(404);
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'day' => 15,
            'month' => 12,
            'year' => 2025,
        ]);

        $response = $this->get(
            "/journals/{$journal->slug}/entries/{$entry->year}/{$entry->month}/{$entry->day}",
        );

        $response->assertRedirect('/login');
    }
}
