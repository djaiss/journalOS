<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Modules\Sleep;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleSleep;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class SleepBedTimeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_bedtime_and_redirects(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);
        ModuleSleep::factory()->create([
            'journal_entry_id' => $entry->id,
            'bedtime' => '22:00',
            'wake_up_time' => '06:00',
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/sleep",
            ['bedtime' => '<b>23:30</b>'],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh();
        $entry->load('moduleSleep');
        $this->assertSame('23:30', $entry->moduleSleep->bedtime);
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->put(
            "/journals/{$journal->slug}/entries/2024/6/15/sleep",
            ['bedtime' => '23:30'],
        );

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_returns_404_for_unauthorized_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/sleep",
            ['bedtime' => '23:30'],
        );

        $response->assertStatus(404);
    }
}
