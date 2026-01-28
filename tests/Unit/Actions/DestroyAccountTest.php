<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\DestroyAccount;
use App\Jobs\DeleteRelatedAccountData;
use App\Mail\AccountDestroyed;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use Illuminate\Support\Facades\Queue;

final class DestroyAccountTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_destroys_a_user_account(): void
    {
        Mail::fake();
        config(['services.journalos.account_deletion_notification_email' => 'regis@journalos.cloud']);

        $user = User::factory()->create();

        (new DestroyAccount(
            user: $user,
            reason: 'the service is not working',
        ))->execute();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);

        $this->assertDatabaseHas('account_deletion_reasons', [
            'reason' => 'the service is not working',
        ]);

        Mail::assertQueued(AccountDestroyed::class, function (AccountDestroyed $job): bool {
            return $job->reason === 'the service is not working'
                && $job->to[0]['address'] === 'regis@journalos.cloud';
        });

        Queue::assertPushedOn(
            queue: 'low',
            job: DeleteRelatedAccountData::class,
            callback: function (DeleteRelatedAccountData $job) use ($user): bool {
                return $job->userId === $user->id;
            },
        );
    }
}
