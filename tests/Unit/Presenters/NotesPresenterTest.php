<?php

declare(strict_types = 1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use App\View\Presenters\NotesPresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class NotesPresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_generates_correct_urls(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 1,
            'day' => 15,
        ]);

        $presenter = new NotesPresenter($entry);
        $data = $presenter->build();

        $this->assertStringContainsString(
            '/journals/' . $journal->slug . '/entries/2024/1/15/notes/edit',
            $data['notes_edit_url'],
        );
        $this->assertStringContainsString(
            '/journals/' . $journal->slug . '/entries/2024/1/15/notes/reset',
            $data['reset_url'],
        );
    }
}
