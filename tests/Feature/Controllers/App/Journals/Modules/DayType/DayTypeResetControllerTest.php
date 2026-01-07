<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Modules\DayType;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleDayType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class DayTypeResetControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resets_day_type_data_and_redirects(): void
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
        ModuleDayType::factory()->create([
            'journal_entry_id' => $entry->id,
            'day_type' => 'workday',
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2022/1/1/day-type/reset",
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2022/1/1");
        $response->assertSessionHas('status');

        $entry->refresh();
        $this->assertNull($entry->moduleDayType);
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

        $response = $this->put("/journals/{$journal->slug}/entries/2022/1/1/day-type/reset");

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
        ModuleDayType::factory()->create([
            'journal_entry_id' => $entry->id,
            'day_type' => 'vacation',
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2022/1/1/day-type/reset",
        );

        $response->assertStatus(404);

        $entry->refresh();
        $this->assertNotNull($entry->moduleDayType);
    }
}
