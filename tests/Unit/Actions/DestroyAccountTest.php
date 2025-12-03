<?php

declare(strict_types=1);

use App\Actions\DestroyAccount;
use App\Models\User;
use App\Mail\AccountDestroyed;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Mail;

it('destroys a user account', function (): void {
    Queue::fake();
    Mail::fake();
    config(['collectos.account_deletion_notification_email' => 'regis@collectos.com']);

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
            && $job->to[0]['address'] === 'regis@collectos.com';
    });
});
