<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Journals\Modules\Meals;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MealsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_meal_presence_with_multiple_values(): void
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

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/meals",
            ['meal_presence' => ['breakfast', 'dinner']],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh()->load('moduleMeals');
        $this->assertEquals(['breakfast', 'dinner'], $entry->moduleMeals->meal_presence);
    }

    #[Test]
    public function it_updates_meal_type(): void
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

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/meals",
            ['meal_type' => 'home_cooked'],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh()->load('moduleMeals');
        $this->assertEquals('home_cooked', $entry->moduleMeals->meal_type);
    }

    #[Test]
    public function it_updates_social_context(): void
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

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/meals",
            ['social_context' => 'friends'],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh()->load('moduleMeals');
        $this->assertEquals('friends', $entry->moduleMeals->social_context);
    }

    #[Test]
    public function it_updates_notes_when_enabled(): void
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

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/meals",
            ['has_notes' => 'yes', 'notes' => 'Late dinner.'],
        );

        $response->assertRedirectContains("/journals/{$journal->slug}/entries/2024/6/15");
        $response->assertSessionHas('status');

        $entry->refresh()->load('moduleMeals');
        $this->assertEquals('yes', $entry->moduleMeals->has_notes);
        $this->assertEquals('Late dinner.', $entry->moduleMeals->notes);
    }

    #[Test]
    public function it_validates_meal_presence_must_be_array(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/meals",
            ['meal_presence' => 'breakfast'],
        );

        $response->assertSessionHasErrors('meal_presence');
    }

    #[Test]
    public function it_validates_required_fields(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 6,
            'day' => 15,
        ]);

        $response = $this->actingAs($user)->put(
            "/journals/{$journal->slug}/entries/2024/6/15/meals",
            [],
        );

        $response->assertSessionHasErrors(['meal_presence', 'meal_type', 'social_context', 'has_notes', 'notes']);
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

        $response = $this->put("/journals/{$journal->slug}/entries/2024/6/15/meals", [
            'meal_type' => 'takeout',
        ]);

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
            "/journals/{$journal->slug}/entries/2024/6/15/meals",
            ['meal_type' => 'takeout'],
        );

        $response->assertNotFound();
    }
}
