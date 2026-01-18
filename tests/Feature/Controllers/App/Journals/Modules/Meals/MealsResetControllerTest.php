<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Modules\Meals;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleMeals;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MealsResetControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resets_meals_data(): void
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
        ModuleMeals::factory()->create([
            'journal_entry_id' => $entry->id,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/meals/reset",
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh()->load('moduleMeals');
        $this->assertNull($entry->moduleMeals);
    }

    #[Test]
    public function it_redirects_guests_to_login(): void
    {
        $journal = Journal::factory()->create();
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->put("/journals/{$journal->slug}/entries/2024/6/15/meals/reset");

        $response->assertRedirect('/login');
    }

    #[Test]
    public function it_returns_404_for_unauthorized_entry(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/meals/reset",
        );

        $response->assertNotFound();
    }
}
