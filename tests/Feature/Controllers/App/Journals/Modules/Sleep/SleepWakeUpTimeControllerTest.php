<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Modules\Sleep;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleSleep;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SleepWakeUpTimeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_wake_up_time_and_redirects(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'day' => 15,
            'month' => 1,
            'year' => 2024,
        ]);
        ModuleSleep::factory()->create([
            'journal_entry_id' => $entry->id,
            'bedtime' => '22:00',
            'wake_up_time' => '06:00',
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/1/15/sleep",
            ['wake_up_time' => '<b>07:30</b>'],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/1/15");
        $response->assertSessionHas('status');

        $entry->refresh();
        $entry->load('moduleSleep');
        $this->assertSame('07:30', $entry->moduleSleep->wake_up_time);
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'day' => 15,
            'month' => 1,
            'year' => 2024,
        ]);

        $response = $this->put(
            "/journals/{$journal->slug}/entries/2024/1/15/sleep",
            ['wake_up_time' => '07:30'],
        );

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_returns_404_for_unauthorized_entry(): void
    {
        $entryDate = [
            'day' => 15,
            'month' => 1,
            'year' => 2024,
        ];
        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'day' => 15,
            'month' => 1,
            'year' => 2024,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/1/15/sleep",
            ['wake_up_time' => '07:30'],
        );

        $response->assertStatus(404);
    }
}
