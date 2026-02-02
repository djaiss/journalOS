<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleMood;
use App\Models\ModuleWork;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalEntryResetControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resets_day_data_and_redirects(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'can_edit_past' => true,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2022,
            'month' => 1,
            'day' => 1,
            'notes' => '<p>Reset this.</p>',
        ]);
        ModuleMood::factory()->create([
            'journal_entry_id' => $entry->id,
            'mood' => 'happy',
        ]);
        ModuleWork::factory()->create([
            'journal_entry_id' => $entry->id,
            'worked' => 'yes',
            'work_mode' => 'remote',
            'work_load' => 'heavy',
            'work_procrastinated' => 'no',
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2022/1/1/reset",
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2022/1/1");
        $response->assertSessionHas('status');

        $entry->refresh();

        $this->assertNull($entry->moduleMood);
        $this->assertNull($entry->moduleWork);
        $this->assertSame('', mb_trim($entry->notes->toPlainText()));
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

        $response = $this->put("/journals/{$journal->slug}/entries/2022/1/1/reset");

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
            'notes' => '<p>Reset this.</p>',
        ]);
        ModuleMood::factory()->create([
            'journal_entry_id' => $entry->id,
            'mood' => 'happy',
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2022/1/1/reset",
        );

        $response->assertStatus(404);

        $entry->refresh();
        $this->assertNotNull($entry->moduleMood);
        $this->assertSame('Reset this.', mb_trim($entry->notes->toPlainText()));
    }
}
