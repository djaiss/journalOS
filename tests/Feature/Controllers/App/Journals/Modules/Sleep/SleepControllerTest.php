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

final class SleepControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_sleep_module_with_default_times(): void
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
            'bedtime' => '22:30',
            'wake_up_time' => '06:45',
        ]);

        $response = $this->actingAs($user)->get(
            "/journals/{$journal->slug}/entries/2024/6/15/sleep/20:00/06:00",
        );

        $response->assertStatus(200);
        $response->assertViewIs('app.journal.entry.partials.sleep');
    }

    #[Test]
    public function it_returns_404_for_unauthorized_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->get(
            "/journals/{$journal->slug}/entries/2024/6/15/sleep/20:00/06:00",
        );

        $response->assertStatus(404);
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->get(
            "/journals/{$journal->slug}/entries/2024/6/15/sleep/20:00/06:00",
        );

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_returns_404_for_invalid_bedtime_format(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->get(
            "/journals/{$journal->slug}/entries/2024/6/15/sleep/25:00/06:00",
        );

        $response->assertStatus(404);
    }

    #[Test]
    public function it_returns_404_for_invalid_wake_up_time_format(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->get(
            "/journals/{$journal->slug}/entries/2024/6/15/sleep/20:00/25:00",
        );

        $response->assertStatus(404);
    }

    #[Test]
    public function it_returns_404_for_invalid_bedtime_minutes(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->get(
            "/journals/{$journal->slug}/entries/2024/6/15/sleep/20:75/06:00",
        );

        $response->assertStatus(404);
    }

    #[Test]
    public function it_returns_404_for_invalid_wake_up_time_minutes(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create([
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->get(
            "/journals/{$journal->slug}/entries/2024/6/15/sleep/20:00/06:75",
        );

        $response->assertStatus(404);
    }

    #[Test]
    public function it_passes_module_data_to_view(): void
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
            'bedtime' => '22:30',
            'wake_up_time' => '06:45',
        ]);

        $response = $this->actingAs($user)->get(
            "/journals/{$journal->slug}/entries/2024/6/15/sleep/20:00/06:00",
        );

        $response->assertViewHas('module');
        $this->assertIsArray($response['module']);
    }
}
