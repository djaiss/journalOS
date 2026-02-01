<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Journals\Notes;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class NotesStoreControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_stores_notes_and_redirects_to_the_entry(): void
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

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2026/1/8/notes",
            ['notes' => 'Hello from Trix'],
        );

        $response->assertRedirect("/journals/{$journal->slug}/entries/2026/1/8");
        $response->assertSessionHas('status', __('Changes saved'));

        $this->assertStringContainsString('Hello from Trix', (string) $entry->fresh()->notes);
    }

    #[Test]
    public function it_requires_notes(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'day' => 8,
            'month' => 1,
            'year' => 2026,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2026/1/8/notes",
            ['notes' => ''],
        );

        $response->assertSessionHasErrors('notes');
    }

    #[Test]
    public function it_returns_404_for_unauthorized_entries(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'day' => 8,
            'month' => 1,
            'year' => 2026,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2026/1/8/notes",
            ['notes' => 'Hello from Trix'],
        );

        $response->assertStatus(404);
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $journal = Journal::factory()->create();
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'day' => 8,
            'month' => 1,
            'year' => 2026,
        ]);

        $response = $this->put(
            "/journals/{$journal->slug}/entries/2026/1/8/notes",
            ['notes' => 'Hello from Trix'],
        );

        $response->assertRedirect('/login');
    }
}
