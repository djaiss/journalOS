<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Journals;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\Layout;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalEntryControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_a_journal_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'layout_id' => $layout->id,
            'day' => 15,
            'month' => 6,
            'year' => 2024,
        ]);

        $response = $this->actingAs($user)->get(
            "/journals/{$journal->slug}/entries/2024/6/15",
        );

        $response->assertStatus(302);
    }

    #[Test]
    public function it_returns_404_for_unauthorized_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'layout_id' => $layout->id,
            'day' => 15,
            'month' => 6,
            'year' => 2024,
        ]);

        $response = $this->actingAs($user)->get(
            "/journals/{$journal->slug}/entries/2024/6/15",
        );

        $response->assertStatus(404);
    }

    #[Test]
    public function it_shows_a_journal_entry_report(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'day' => 15,
            'month' => 6,
            'year' => 2024,
        ]);

        $response = $this->actingAs($user)->get(
            "/journals/{$journal->slug}/entries/2024/6/15/report",
        );

        $response->assertStatus(200);
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $journal = Journal::factory()->create();
        $layout = Layout::factory()->create([
            'journal_id' => $journal->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'layout_id' => $layout->id,
            'day' => 15,
            'month' => 6,
            'year' => 2024,
        ]);

        $response = $this->get(
            "/journals/{$journal->slug}/entries/2024/6/15",
        );

        $response->assertRedirect('/login');
    }
}
