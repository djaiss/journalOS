<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\LogActivityIntensity;
use App\Actions\LogActivityType;
use App\Actions\LogHasDonePhysicalActivity;
use App\Models\Journal;
use App\Models\JournalEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LogPhysicalActivityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_has_done_physical_activity_yes(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        $entry = new LogHasDonePhysicalActivity(
            user: $user,
            entry: $entry,
            hasDonePhysicalActivity: 'yes',
        )->execute();

        $this->assertEquals('yes', $entry->has_done_physical_activity);
    }

    #[Test]
    public function it_logs_has_done_physical_activity_no(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        $entry = new LogHasDonePhysicalActivity(
            user: $user,
            entry: $entry,
            hasDonePhysicalActivity: 'no',
        )->execute();

        $this->assertEquals('no', $entry->has_done_physical_activity);
    }

    #[Test]
    public function it_logs_activity_type(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        $entry = new LogActivityType(
            user: $user,
            entry: $entry,
            activityType: 'running',
        )->execute();

        $this->assertEquals('running', $entry->activity_type);
    }

    #[Test]
    public function it_logs_activity_intensity(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        $entry = new LogActivityIntensity(
            user: $user,
            entry: $entry,
            activityIntensity: 'moderate',
        )->execute();

        $this->assertEquals('moderate', $entry->activity_intensity);
    }

    #[Test]
    public function it_throws_when_entry_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $journal = Journal::factory()->for($otherUser)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        new LogHasDonePhysicalActivity(
            user: $user,
            entry: $entry,
            hasDonePhysicalActivity: 'yes',
        )->execute();
    }

    #[Test]
    public function it_throws_when_has_done_physical_activity_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        new LogHasDonePhysicalActivity(
            user: $user,
            entry: $entry,
            hasDonePhysicalActivity: 'invalid',
        )->execute();
    }

    #[Test]
    public function it_throws_when_activity_type_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        new LogActivityType(
            user: $user,
            entry: $entry,
            activityType: 'invalid',
        )->execute();
    }

    #[Test]
    public function it_throws_when_activity_intensity_is_invalid(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->for($user)->create();
        $entry = JournalEntry::factory()->for($journal)->create();

        new LogActivityIntensity(
            user: $user,
            entry: $entry,
            activityIntensity: 'invalid',
        )->execute();
    }
}
