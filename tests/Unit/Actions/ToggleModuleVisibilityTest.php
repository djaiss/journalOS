<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\ToggleModuleVisibility;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ToggleModuleVisibilityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_toggles_a_module_visibility(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
            'show_sleep_module' => true,
        ]);

        $updatedJournal = (new ToggleModuleVisibility(
            user: $user,
            journal: $journal,
            moduleName: 'sleep',
        ))->execute();

        $this->assertFalse($updatedJournal->show_sleep_module);

        $this->assertDatabaseHas('journals', [
            'id' => $journal->id,
            'show_sleep_module' => false,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user, $journal): bool {
                return $job->action === 'module_visibility_toggled'
                    && $job->user->is($user)
                    && $job->journal?->is($journal);
            },
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: function (UpdateUserLastActivityDate $job) use ($user): bool {
                return $job->user->is($user);
            },
        );
    }

    #[Test]
    public function it_throws_when_module_is_not_defined(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        (new ToggleModuleVisibility(
            user: $user,
            journal: $journal,
            moduleName: 'nonexistent',
        ))->execute();
    }

    #[Test]
    public function it_throws_when_journal_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $otherJournal = Journal::factory()->create();

        (new ToggleModuleVisibility(
            user: $user,
            journal: $otherJournal,
            moduleName: 'sleep',
        ))->execute();
    }
}
