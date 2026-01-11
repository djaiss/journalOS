<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogTravel;
use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LogTravelModeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_single_travel_mode(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $result = (new LogTravel(
            user: $user,
            entry: $entry,
            hasTraveled: null,
            travelModes: ['car'],
        ))->execute();

        $this->assertEquals(['car'], $result->moduleTravel->travel_mode);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'travel_logged' && $job->user->id === $user->id;
            },
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: function (UpdateUserLastActivityDate $job) use ($user): bool {
                return $job->user->id === $user->id;
            },
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: CheckPresenceOfContentInJournalEntry::class,
            callback: function (CheckPresenceOfContentInJournalEntry $job) use ($entry): bool {
                return $job->entry->id === $entry->id;
            },
        );
    }

    #[Test]
    public function it_logs_multiple_travel_modes(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $result = (new LogTravel(
            user: $user,
            entry: $entry,
            hasTraveled: null,
            travelModes: ['car', 'plane', 'train'],
        ))->execute();

        $this->assertEquals(['car', 'plane', 'train'], $result->moduleTravel->travel_mode);
        $this->assertContains('car', $result->moduleTravel->travel_mode);
        $this->assertContains('plane', $result->moduleTravel->travel_mode);
        $this->assertContains('train', $result->moduleTravel->travel_mode);
    }

    #[Test]
    public function it_logs_all_valid_travel_modes(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $allModes = ['car', 'plane', 'train', 'bike', 'bus', 'walk', 'boat', 'other'];

        $result = (new LogTravel(
            user: $user,
            entry: $entry,
            hasTraveled: null,
            travelModes: $allModes,
        ))->execute();

        $this->assertEquals($allModes, $result->moduleTravel->travel_mode);
        $this->assertCount(8, $result->moduleTravel->travel_mode);

        Queue::assertPushedOn(
            queue: 'low',
            job: CheckPresenceOfContentInJournalEntry::class,
            callback: function (CheckPresenceOfContentInJournalEntry $job) use ($entry): bool {
                return $job->entry->id === $entry->id;
            },
        );
    }

    #[Test]
    public function it_throws_when_journal_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Journal entry not found');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        (new LogTravel(
            user: $user,
            entry: $entry,
            hasTraveled: null,
            travelModes: ['car'],
        ))->execute();
    }

    #[Test]
    public function it_throws_when_travel_modes_is_empty(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        (new LogTravel(
            user: $user,
            entry: $entry,
            hasTraveled: null,
            travelModes: [],
        ))->execute();
    }

    #[Test]
    public function it_throws_when_travel_mode_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        (new LogTravel(
            user: $user,
            entry: $entry,
            hasTraveled: null,
            travelModes: ['car', 'invalid', 'plane'],
        ))->execute();
    }
}
