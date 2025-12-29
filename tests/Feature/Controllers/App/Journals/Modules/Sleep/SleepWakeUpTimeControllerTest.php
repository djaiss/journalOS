<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Modules\Sleep;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class SleepWakeUpTimeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_wake_up_time_and_redirects(): void
    {
        $entryDate = [
            'day' => 15,
            'month' => 1,
            'year' => 2024,
        ];
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            ...$entryDate,
            'bedtime' => '22:00',
            'wake_up_time' => '06:00',
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/{$entry->year}/{$entry->month}/{$entry->day}/sleep/wake_up_time",
            ['wake_up_time' => '07:30'],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/{$entry->year}/{$entry->month}/{$entry->day}");
        $response->assertSessionHas('status');
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $entryDate = [
            'day' => 15,
            'month' => 1,
            'year' => 2024,
        ];
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->for($journal)->create($entryDate);

        $response = $this->put(
            "/journals/{$journal->slug}/entries/{$entry->year}/{$entry->month}/{$entry->day}/sleep/wake_up_time",
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
        $entry = JournalEntry::factory()->for($journal)->create($entryDate);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/{$entry->year}/{$entry->month}/{$entry->day}/sleep/wake_up_time",
            ['wake_up_time' => '07:30'],
        );

        $response->assertStatus(404);
    }
}
