<?php

declare(strict_types=1);

namespace Tests\Unit\Presenters;

use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleShopping;
use App\View\Presenters\ShoppingModulePresenter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ShoppingModulePresenterTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_builds_shopping_module_data(): void
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

        $presenter = new ShoppingModulePresenter($entry);
        $result = $presenter->build();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('has_shopped_today', $result);
        $this->assertArrayHasKey('shopping_url', $result);
        $this->assertArrayHasKey('shopping_type', $result);
        $this->assertArrayHasKey('shopping_types', $result);
        $this->assertArrayHasKey('shopping_intent', $result);
        $this->assertArrayHasKey('shopping_intents', $result);
        $this->assertArrayHasKey('shopping_context', $result);
        $this->assertArrayHasKey('shopping_contexts', $result);
        $this->assertArrayHasKey('shopping_for', $result);
        $this->assertArrayHasKey('shopping_for_options', $result);
        $this->assertArrayHasKey('reset_url', $result);
        $this->assertArrayHasKey('display_reset', $result);

        $this->assertEquals(
            route('journal.entry.shopping.update', [
                'slug' => $entry->journal->slug,
                'year' => $entry->year,
                'month' => $entry->month,
                'day' => $entry->day,
            ]),
            $result['shopping_url'],
        );

        $this->assertEquals(
            route('journal.entry.shopping.reset', [
                'slug' => $entry->journal->slug,
                'year' => $entry->year,
                'month' => $entry->month,
                'day' => $entry->day,
            ]),
            $result['reset_url'],
        );

        $this->assertCount(8, $result['shopping_types']);
        $this->assertEquals('groceries', $result['shopping_types'][0]['value']);
        $this->assertEquals('clothes', $result['shopping_types'][1]['value']);
        $this->assertEquals('electronics_tech', $result['shopping_types'][2]['value']);
        $this->assertEquals('household_essentials', $result['shopping_types'][3]['value']);
        $this->assertEquals('books_media', $result['shopping_types'][4]['value']);
        $this->assertEquals('gifts', $result['shopping_types'][5]['value']);
        $this->assertEquals('online_shopping', $result['shopping_types'][6]['value']);
        $this->assertEquals('other', $result['shopping_types'][7]['value']);

        $this->assertFalse($result['display_reset']);
    }

    #[Test]
    public function it_marks_selected_shopping_types(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleShopping::factory()->create([
            'journal_entry_id' => $entry->id,
            'shopping_type' => ['groceries', 'books_media'],
        ]);

        $presenter = new ShoppingModulePresenter($entry);
        $result = $presenter->build();

        $selectedTypes = collect($result['shopping_types'])->filter(fn($type) => $type['is_selected']);
        $this->assertCount(2, $selectedTypes);
        $this->assertEquals(['groceries', 'books_media'], $selectedTypes->pluck('value')->values()->all());
    }

    #[Test]
    public function it_translates_shopping_type_labels(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $presenter = new ShoppingModulePresenter($entry);
        $result = $presenter->build();

        $this->assertEquals(__('Groceries'), $result['shopping_types'][0]['label']);
        $this->assertEquals(__('Clothes'), $result['shopping_types'][1]['label']);
        $this->assertEquals(__('Electronics / tech'), $result['shopping_types'][2]['label']);
        $this->assertEquals(__('Household / essentials'), $result['shopping_types'][3]['label']);
        $this->assertEquals(__('Books / media'), $result['shopping_types'][4]['label']);
        $this->assertEquals(__('Gifts'), $result['shopping_types'][5]['label']);
        $this->assertEquals(__('Online shopping'), $result['shopping_types'][6]['label']);
        $this->assertEquals(__('Other'), $result['shopping_types'][7]['label']);
    }

    #[Test]
    public function it_displays_reset_when_shopping_data_is_set(): void
    {
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleShopping::factory()->create([
            'journal_entry_id' => $entry->id,
            'shopping_for' => 'for_self',
        ]);

        $presenter = new ShoppingModulePresenter($entry);
        $result = $presenter->build();

        $this->assertTrue($result['display_reset']);
    }
}
