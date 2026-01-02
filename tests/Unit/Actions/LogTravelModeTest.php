<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogTravelMode;
use App\Jobs\CheckPresenceOfContentInJournalEntry;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use InvalidArgumentException;
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
        $entry = JournalEntry::factory()->for($journal)->create([
            'travel_mode' => null,
        ]);

        $result = (new LogTravelMode(
            user: $user,
            entry: $entry,
            travelModes: ['car'],
        ))->execute();

        $this->assertEquals(['car'], $result->travel_mode);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'travel_mode_logged' && $job->user->id === $user->id;
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
        $entry = JournalEntry::factory()->for($journal)->create([
            'travel_mode' => null,
        ]);

        $result = (new LogTravelMode(
            user: $user,
            entry: $entry,
            travelModes: ['car', 'plane', 'train'],
        ))->execute();

        $this->assertEquals(['car', 'plane', 'train'], $result->travel_mode);
        $this->assertContains('car', $result->travel_mode);
        $this->assertContains('plane', $result->travel_mode);
        $this->assertContains('train', $result->travel_mode);
    }

    #[Test]
    public function it_logs_all_valid_travel_modes(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->for($journal)->create([
            'travel_mode' => null,
        ]);

        $allModes = ['car', 'plane', 'train', 'bike', 'bus', 'walk', 'boat', 'other'];

        $result = (new LogTravelMode(
            user: $user,
            entry: $entry,
            travelModes: $allModes,
        ))->execute();

        $this->assertEquals($allModes, $result->travel_mode);
        $this->assertCount(8, $result->travel_mode);

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
        $this->expectExceptionMessage('Journal not found');

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $entry = JournalEntry::factory()->for($journal)->create();

        (new LogTravelMode(
            user: $user,
            entry: $entry,
            travelModes: ['car'],
        ))->execute();
    }

    #[Test]
    public function it_throws_when_travel_modes_is_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('travelModes cannot be empty');

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->for($journal)->create();

        (new LogTravelMode(
            user: $user,
            entry: $entry,
            travelModes: [],
        ))->execute();
    }

    #[Test]
    public function it_throws_when_travel_mode_is_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Each travelMode must be one of: car, plane, train, bike, bus, walk, boat, other');

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->for($journal)->create();

        (new LogTravelMode(
            user: $user,
            entry: $entry,
            travelModes: ['car', 'invalid', 'plane'],
        ))->execute();
    }
}
