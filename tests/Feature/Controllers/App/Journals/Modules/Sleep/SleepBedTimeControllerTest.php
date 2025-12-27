<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Modules\Sleep;

use App\Models\Journal;
use App\Models\JournalEntry;
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
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'bedtime' => '22:00',
            'wake_up_time' => '06:00',
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/{$entry->year}/{$entry->month}/{$entry->day}/sleep/bedtime",
            ['bedtime' => '23:30'],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/{$entry->year}/{$entry->month}/{$entry->day}");
        $response->assertSessionHas('status');
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        $response = $this->put(
            "/journals/{$journal->slug}/entries/{$entry->year}/{$entry->month}/{$entry->day}/sleep/bedtime",
            ['bedtime' => '23:30'],
        );

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_returns_404_for_unauthorized_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/{$entry->year}/{$entry->month}/{$entry->day}/sleep/bedtime",
            ['bedtime' => '23:30'],
        );

        $response->assertStatus(404);
    }
}
