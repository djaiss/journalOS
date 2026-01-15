<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\SetActiveLayout;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\Layout;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SetActiveLayoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_sets_a_layout_as_active(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);
        $inactiveLayout = Layout::factory()->create([
            'journal_id' => $journal->id,
            'name' => 'Inactive Layout',
            'is_active' => false,
        ]);
        $targetLayout = Layout::factory()->create([
            'journal_id' => $journal->id,
            'name' => 'Active Layout',
            'is_active' => false,
        ]);

        $updatedLayout = (new SetActiveLayout(
            user: $user,
            layout: $targetLayout,
        ))->execute();

        $this->assertTrue($updatedLayout->is_active);
        $this->assertFalse($inactiveLayout->fresh()->is_active);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user, $journal, $targetLayout): bool {
                return $job->action === 'layout_set_active'
                    && $job->user->id === $user->id
                    && $job->journal?->id === $journal->id
                    && str_contains($job->description, $targetLayout->name)
                    && str_contains($job->description, $journal->name);
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
    public function it_throws_an_exception_if_layout_does_not_belong_to_user(): void
    {
        $this->expectException(ModelNotFoundException::class);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $otherJournal = Journal::factory()->create([
            'user_id' => $otherUser->id,
        ]);
        $layout = Layout::factory()->create([
            'journal_id' => $otherJournal->id,
        ]);

        (new SetActiveLayout(
            user: $user,
            layout: $layout,
        ))->execute();
    }
}
