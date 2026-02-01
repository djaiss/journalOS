<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\LogPhysicalActivity;
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

final class LogPhysicalActivityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_logs_has_done_physical_activity_yes(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogPhysicalActivity(
            user: $user,
            entry: $entry,
            hasDonePhysicalActivity: 'yes',
            activityType: null,
            activityIntensity: null,
        )->execute();

        $this->assertEquals('yes', $entry->modulePhysicalActivity->has_done_physical_activity);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job) => $job->action === 'physical_activity_logged' && $job->user->id === $user->id,
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: fn (UpdateUserLastActivityDate $job) => $job->user->id === $user->id,
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: CheckPresenceOfContentInJournalEntry::class,
            callback: fn (CheckPresenceOfContentInJournalEntry $job) => $job->entry->id === $entry->id,
        );
    }

    #[Test]
    public function it_logs_has_done_physical_activity_no(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogPhysicalActivity(
            user: $user,
            entry: $entry,
            hasDonePhysicalActivity: 'no',
            activityType: null,
            activityIntensity: null,
        )->execute();

        $this->assertEquals('no', $entry->modulePhysicalActivity->has_done_physical_activity);
    }

    #[Test]
    public function it_logs_activity_type(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogPhysicalActivity(
            user: $user,
            entry: $entry,
            hasDonePhysicalActivity: null,
            activityType: 'running',
            activityIntensity: null,
        )->execute();

        $this->assertEquals('running', $entry->modulePhysicalActivity->activity_type);
    }

    #[Test]
    public function it_logs_activity_intensity(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogPhysicalActivity(
            user: $user,
            entry: $entry,
            hasDonePhysicalActivity: null,
            activityType: null,
            activityIntensity: 'moderate',
        )->execute();

        $this->assertEquals('moderate', $entry->modulePhysicalActivity->activity_intensity);
    }

    #[Test]
    public function it_throws_when_entry_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogPhysicalActivity(
            user: $user,
            entry: $entry,
            hasDonePhysicalActivity: 'yes',
            activityType: null,
            activityIntensity: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_has_done_physical_activity_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogPhysicalActivity(
            user: $user,
            entry: $entry,
            hasDonePhysicalActivity: 'invalid',
            activityType: null,
            activityIntensity: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_activity_type_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogPhysicalActivity(
            user: $user,
            entry: $entry,
            hasDonePhysicalActivity: null,
            activityType: 'invalid',
            activityIntensity: null,
        )->execute();
    }

    #[Test]
    public function it_throws_when_activity_intensity_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogPhysicalActivity(
            user: $user,
            entry: $entry,
            hasDonePhysicalActivity: null,
            activityType: null,
            activityIntensity: 'invalid',
        )->execute();
    }
}
