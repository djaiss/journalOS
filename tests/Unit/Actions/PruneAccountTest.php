<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\PruneAccount;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PruneAccountTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_prunes_an_account(): void
    {
        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        new PruneAccount(
            user: $user,
        )->execute();

        $this->assertDatabaseMissing('journals', [
            'id' => $journal->id,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: fn (UpdateUserLastActivityDate $job) => $job->user->id === $user->id,
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job) => (
                    $job->action === 'account_pruning'
                    && $job->user->id === $user->id
                    && $job->description === 'Deleted all journals and related data from your account'
                ),
        );
    }
}
