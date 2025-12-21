<?php

declare(strict_types=1);

namespace Tests\Unit\ViewModels\Journal;

use App\Actions\GenerateJournalAvatar;
use App\Http\ViewModels\Journal\JournalIndexViewModel;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class JournalIndexViewModelTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_journals_for_the_user_with_expected_shape(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $firstJournal = Journal::factory()->for($user)->create([
            'name' => 'Travel',
        ]);

        $secondJournal = Journal::factory()->for($user)->create([
            'name' => 'Work',
        ]);

        Journal::factory()->for($otherUser)->create([
            'name' => 'Should be hidden',
        ]);

        $journals = new JournalIndexViewModel(
            user: $user,
        )->journals();

        $this->assertCount(2, $journals);

        $this->assertEquals([
            [
                'id' => $firstJournal->id,
                'name' => 'Travel',
                'slug' => $firstJournal->slug,
                'avatar' => (new GenerateJournalAvatar($firstJournal->id . '-' . $firstJournal->name))->execute(),
            ],
            [
                'id' => $secondJournal->id,
                'name' => 'Work',
                'slug' => $secondJournal->slug,
                'avatar' => (new GenerateJournalAvatar($secondJournal->id . '-' . $secondJournal->name))->execute(),
            ],
        ], $journals->map(fn(object $journal): array => (array) $journal)->all());
    }
}
