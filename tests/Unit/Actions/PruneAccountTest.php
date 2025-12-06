<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\User;
use App\Actions\PruneAccount;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PruneAccountTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_prunes_an_account(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $journal = Journal::factory()->create([
            'user_id' => $user->id,
        ]);

        (new PruneAccount(
            user: $user,
        ))->execute();

        $this->assertDatabaseMissing('journals', [
            'id' => $journal->id,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: function (UpdateUserLastActivityDate $job) use ($user): bool {
                return $job->user->id === $user->id;
            },
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'account_pruning'
                    && $job->user->id === $user->id
                    && $job->description === 'Deleted all journals and related data from your account';
            },
        );
    }
}
