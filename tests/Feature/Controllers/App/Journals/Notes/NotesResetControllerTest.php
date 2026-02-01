<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Journals\Notes;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class NotesResetControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resets_notes_for_entry(): void
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

        $entry
            ->richTextNotes()
            ->create([
                'field' => 'notes',
                'body' => '<div>Some notes</div>',
            ]);

        $this->assertDatabaseHas('rich_texts', [
            'record_id' => $entry->id,
            'field' => 'notes',
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2026/1/8/notes/reset",
        );

        $response->assertRedirect("/journals/{$journal->slug}/entries/2026/1/8/edit");
        $response->assertSessionHas('status', __('Changes saved'));

        $entry->refresh();
        $richTextNotes = $entry->richTextNotes;
        $this->assertNotNull($richTextNotes);
        $this->assertEquals('', $richTextNotes->toPlainText());
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

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2026/1/8/notes/reset",
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

        $response = $this->put(
            "/journals/{$journal->slug}/entries/2026/1/8/notes/reset",
        );

        $response->assertRedirect('/login');
    }
}
