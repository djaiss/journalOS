<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Notes;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class NotesControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_notes_create_page(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'day' => 8,
            'month' => 1,
            'year' => 2026,
        ]);

        $response = $this->actingAs($user)->get(
            "/journals/{$journal->slug}/entries/2026/1/8/notes/edit",
        );

        $response->assertStatus(200);
        $response->assertViewIs('app.journal.entry.notes.edit');
        $response->assertViewHas('journal', $journal);
        $response->assertViewHas('entry');
    }

    #[Test]
    public function it_returns_404_for_unauthorized_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'day' => 8,
            'month' => 1,
            'year' => 2026,
        ]);

        $response = $this->actingAs($user)->get(
            "/journals/{$journal->slug}/entries/2026/1/8/notes/create",
        );

        $response->assertStatus(404);
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'day' => 8,
            'month' => 1,
            'year' => 2026,
        ]);

        $response = $this->get(
            "/journals/{$journal->slug}/entries/2026/1/8/notes/edit",
        );

        $response->assertRedirect('/login');
    }
}
