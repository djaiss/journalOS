<?php

declare(strict_types=1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleMeal;
use App\View\Presenters\MealModulePresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MealModulePresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_meal_module_data(): void
    {
        $journal = Journal::factory()->create([
            'slug' => 'my-journal',
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
            'year' => 2024,
            'month' => 12,
            'day' => 25,
        ]);

        $presenter = new MealModulePresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('breakfast', $result);
        $this->assertArrayHasKey('lunch', $result);
        $this->assertArrayHasKey('dinner', $result);
        $this->assertArrayHasKey('snack', $result);
        $this->assertArrayHasKey('meal_type', $result);
        $this->assertArrayHasKey('meal_type_options', $result);
        $this->assertArrayHasKey('social_context', $result);
        $this->assertArrayHasKey('social_context_options', $result);
        $this->assertArrayHasKey('notes', $result);
        $this->assertArrayHasKey('meal_url', $result);
        $this->assertArrayHasKey('reset_url', $result);
        $this->assertArrayHasKey('display_reset', $result);

        $this->assertEquals(
            route('journal.entry.meal.update', [
                'slug' => $entry->journal->slug,
                'year' => $entry->year,
                'month' => $entry->month,
                'day' => $entry->day,
            ]),
            $result['meal_url'],
        );

        $this->assertEquals(
            route('journal.entry.meal.reset', [
                'slug' => $entry->journal->slug,
                'year' => $entry->year,
                'month' => $entry->month,
                'day' => $entry->day,
            ]),
            $result['reset_url'],
        );

        $this->assertCount(4, $result['meal_type_options']);
        $this->assertCount(4, $result['social_context_options']);
        $this->assertFalse($result['display_reset']);
    }

    #[Test]
    public function it_marks_selected_meal_type_and_social_context(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleMeal::factory()->create([
            'journal_entry_id' => $entry->id,
            'meal_type' => 'restaurant',
            'social_context' => 'friends',
        ]);

        $presenter = new MealModulePresenter($entry);
        $result = $presenter->build();

        $selectedMealType = collect($result['meal_type_options'])->firstWhere('is_selected', true);
        $selectedSocialContext = collect($result['social_context_options'])->firstWhere('is_selected', true);

        $this->assertEquals('restaurant', $selectedMealType['value']);
        $this->assertEquals('friends', $selectedSocialContext['value']);
    }

    #[Test]
    public function it_displays_reset_when_meal_data_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleMeal::factory()->create([
            'journal_entry_id' => $entry->id,
            'notes' => 'Dinner with friends.',
        ]);

        $presenter = new MealModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
    }
}
