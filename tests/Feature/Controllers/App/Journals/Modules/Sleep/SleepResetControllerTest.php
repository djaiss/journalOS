<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Modules\Sleep;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class SleepResetControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resets_sleep_data_and_redirects(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'bedtime' => '22:00',
            'wake_up_time' => '06:00',
            'sleep_duration_in_minutes' => 480,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/{$entry->year}/{$entry->month}/{$entry->day}/sleep/reset",
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/{$entry->year}/{$entry->month}/{$entry->day}");
        $response->assertSessionHas('status');

        $entry->refresh();
        $this->assertNull($entry->bedtime);
        $this->assertNull($entry->wake_up_time);
        $this->assertNull($entry->sleep_duration_in_minutes);
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        $response = $this->put(
            "/journals/{$journal->slug}/entries/{$entry->year}/{$entry->month}/{$entry->day}/sleep/reset",
        );

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_returns_404_for_unauthorized_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'bedtime' => '22:00',
            'wake_up_time' => '06:00',
            'sleep_duration_in_minutes' => 480,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/{$entry->year}/{$entry->month}/{$entry->day}/sleep/reset",
        );

        $response->assertStatus(404);

        // Sleep data should not be reset for unauthorized user
        $entry->refresh();
        $this->assertNotNull($entry->bedtime);
        $this->assertNotNull($entry->wake_up_time);
        $this->assertNotNull($entry->sleep_duration_in_minutes);
    }
}
