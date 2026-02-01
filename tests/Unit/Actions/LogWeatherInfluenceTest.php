<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\LogWeatherInfluence;
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

final class LogWeatherInfluenceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_logs_weather_influence_values(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        $entry = new LogWeatherInfluence(
            user: $user,
            entry: $entry,
            moodEffect: 'positive',
            energyEffect: 'boosted',
            plansInfluence: 'slight',
            outsideTime: 'some',
        )->execute();

        $this->assertEquals('positive', $entry->moduleWeatherInfluence->mood_effect);
        $this->assertEquals('boosted', $entry->moduleWeatherInfluence->energy_effect);
        $this->assertEquals('slight', $entry->moduleWeatherInfluence->plans_influence);
        $this->assertEquals('some', $entry->moduleWeatherInfluence->outside_time);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job) => (
                $job->action === 'weather_influence_logged'
                && $job->user->id === $user->id
            ),
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
    public function it_throws_validation_exception_for_invalid_mood_effect_value(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogWeatherInfluence(
            user: $user,
            entry: $entry,
            moodEffect: 'extreme',
            energyEffect: null,
            plansInfluence: null,
            outsideTime: null,
        )->execute();
    }

    #[Test]
    public function it_throws_exception_when_user_does_not_own_journal(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $anotherUser = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $anotherUser->id,
        ]);
        $entry = JournalEntry::factory()->create([
            'journal_id' => $journal->id,
        ]);

        new LogWeatherInfluence(
            user: $user,
            entry: $entry,
            moodEffect: 'positive',
            energyEffect: 'boosted',
            plansInfluence: 'slight',
            outsideTime: 'some',
        )->execute();
    }
}
