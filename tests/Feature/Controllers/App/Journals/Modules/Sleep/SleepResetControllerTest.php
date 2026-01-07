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

final class SleepResetControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resets_sleep_data_and_redirects(): void
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
        ]);
        ModuleSleep::factory()->create([
            'journal_entry_id' => $entry->id,
            'bedtime' => '22:00',
            'wake_up_time' => '06:00',
            'sleep_duration_in_minutes' => 480,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2022/1/1/sleep/reset",
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2022/1/1");
        $response->assertSessionHas('status');

        $entry->refresh();
        $entry->load('moduleSleep');
        $this->assertNull($entry->moduleSleep);
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

        $response = $this->put("/journals/{$journal->slug}/entries/2022/1/1/sleep/reset");

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
        ]);
        ModuleSleep::factory()->create([
            'journal_entry_id' => $entry->id,
            'bedtime' => '22:00',
            'wake_up_time' => '06:00',
            'sleep_duration_in_minutes' => 480,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2022/1/1/sleep/reset",
        );

        $response->assertStatus(404);

        // Sleep data should not be reset for unauthorized user
        $entry->refresh();
        $entry->load('moduleSleep');
        $this->assertNotNull($entry->moduleSleep);
        $this->assertNotNull($entry->moduleSleep->bedtime);
        $this->assertNotNull($entry->moduleSleep->wake_up_time);
        $this->assertNotNull($entry->moduleSleep->sleep_duration_in_minutes);
    }
}
