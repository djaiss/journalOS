<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\ResetMealData;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\ModuleMeal;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ResetMealDataTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_resets_meal_data(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);
        ModuleMeal::factory()->create([
            'journal_entry_id' => $entry->id,
            'breakfast' => 'yes',
        ]);

        $result = (new ResetMealData(
            user: $user,
            entry: $entry,
        ))->execute();

        $this->assertNull($result->moduleMeal);
    }

    #[Test]
    public function it_requires_the_entry_to_belong_to_the_user(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create();
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $this->expectException(ModelNotFoundException::class);

        (new ResetMealData(
            user: $user,
            entry: $entry,
        ))->execute();
    }
}
