<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogTravelMode;
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
    public function it_logs_travel_mode_with_car(): void
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
            travelMode: 'car',
        ))->execute();

        $this->assertEquals('car', $result->travel_mode);

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
    }

    #[Test]
    public function it_logs_travel_mode_with_plane(): void
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
            travelMode: 'plane',
        ))->execute();

        $this->assertEquals('plane', $result->travel_mode);

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
    }

    #[Test]
    public function it_logs_travel_mode_with_train(): void
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
            travelMode: 'train',
        ))->execute();

        $this->assertEquals('train', $result->travel_mode);
    }

    #[Test]
    public function it_logs_travel_mode_with_bike(): void
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
            travelMode: 'bike',
        ))->execute();

        $this->assertEquals('bike', $result->travel_mode);
    }

    #[Test]
    public function it_logs_travel_mode_with_bus(): void
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
            travelMode: 'bus',
        ))->execute();

        $this->assertEquals('bus', $result->travel_mode);
    }

    #[Test]
    public function it_logs_travel_mode_with_walk(): void
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
            travelMode: 'walk',
        ))->execute();

        $this->assertEquals('walk', $result->travel_mode);
    }

    #[Test]
    public function it_logs_travel_mode_with_boat(): void
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
            travelMode: 'boat',
        ))->execute();

        $this->assertEquals('boat', $result->travel_mode);
    }

    #[Test]
    public function it_logs_travel_mode_with_other(): void
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
            travelMode: 'other',
        ))->execute();

        $this->assertEquals('other', $result->travel_mode);
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
            travelMode: 'car',
        ))->execute();
    }

    #[Test]
    public function it_throws_when_travel_mode_is_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('travelMode must be one of: car, plane, train, bike, bus, walk, boat, other');

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->for($journal)->create();

        (new LogTravelMode(
            user: $user,
            entry: $entry,
            travelMode: 'invalid',
        ))->execute();
    }
}
