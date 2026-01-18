<?php

declare(strict_types=1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleMeals;
use App\View\Presenters\MealsModulePresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MealsModulePresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_meals_module_data(): void
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

        $presenter = new MealsModulePresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('meal_presence', $result);
        $this->assertArrayHasKey('meal_presence_options', $result);
        $this->assertArrayHasKey('meal_type', $result);
        $this->assertArrayHasKey('meal_type_options', $result);
        $this->assertArrayHasKey('social_context', $result);
        $this->assertArrayHasKey('social_context_options', $result);
        $this->assertArrayHasKey('has_notes', $result);
        $this->assertArrayHasKey('notes', $result);
        $this->assertArrayHasKey('meals_url', $result);
        $this->assertArrayHasKey('reset_url', $result);
        $this->assertArrayHasKey('display_reset', $result);

        $this->assertEquals(
            route('journal.entry.meals.update', [
                'slug' => $entry->journal->slug,
                'year' => $entry->year,
                'month' => $entry->month,
                'day' => $entry->day,
            ]),
            $result['meals_url'],
        );

        $this->assertEquals(
            route('journal.entry.meals.reset', [
                'slug' => $entry->journal->slug,
                'year' => $entry->year,
                'month' => $entry->month,
                'day' => $entry->day,
            ]),
            $result['reset_url'],
        );

        $this->assertCount(4, $result['meal_presence_options']);
        $this->assertEquals('breakfast', $result['meal_presence_options'][0]['value']);
        $this->assertEquals('lunch', $result['meal_presence_options'][1]['value']);
        $this->assertEquals('dinner', $result['meal_presence_options'][2]['value']);
        $this->assertEquals('snack', $result['meal_presence_options'][3]['value']);

        $this->assertFalse($result['display_reset']);
    }

    #[Test]
    public function it_marks_selected_meal_presence(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleMeals::factory()->create([
            'journal_entry_id' => $entry->id,
            'meal_presence' => ['breakfast', 'snack'],
        ]);

        $presenter = new MealsModulePresenter($entry);
        $result = $presenter->build();

        $selectedPresence = collect($result['meal_presence_options'])->filter(fn($option) => $option['is_selected']);
        $this->assertCount(2, $selectedPresence);
        $this->assertEquals(['breakfast', 'snack'], $selectedPresence->pluck('value')->values()->all());
    }

    #[Test]
    public function it_translates_meal_type_labels(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new MealsModulePresenter($entry);
        $result = $presenter->build();

        $this->assertEquals(__('Home-cooked'), $result['meal_type_options'][0]['label']);
        $this->assertEquals(__('Takeout'), $result['meal_type_options'][1]['label']);
        $this->assertEquals(__('Restaurant'), $result['meal_type_options'][2]['label']);
        $this->assertEquals(__('Work cafeteria'), $result['meal_type_options'][3]['label']);
    }

    #[Test]
    public function it_displays_reset_when_meals_data_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleMeals::factory()->create([
            'journal_entry_id' => $entry->id,
            'social_context' => 'friends',
        ]);

        $presenter = new MealsModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
    }
}
