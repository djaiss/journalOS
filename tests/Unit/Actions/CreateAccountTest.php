<?php

declare(strict_types=1);

use App\Actions\CreateAccount;
use App\Jobs\LogUserAction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Queue;

it('creates an account', function (): void {
    Queue::fake();
    Carbon::setTestNow(Carbon::create(2018, 1, 1));

    $user = (new CreateAccount(
        email: 'michael.scott@dundermifflin.com',
        password: 'password',
        firstName: 'Michael',
        lastName: 'Scott',
    ))->execute();

    expect($user)->toBeInstanceOf(User::class);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => 'michael.scott@dundermifflin.com',
        'first_name' => 'Michael',
        'last_name' => 'Scott',
    ]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: function (LogUserAction $job) use ($user): bool {
            return $job->action === 'account_created' && $job->user->id === $user->id;
        },
    );
});

it('cant create an account with the same email', function (): void {
    User::factory()->create([
        'email' => 'michael.scott@dundermifflin.com',
    ]);

    expect(fn() => (new CreateAccount(
        email: 'michael.scott@dundermifflin.com',
        password: 'password',
        firstName: 'Michael',
        lastName: 'Scott',
    ))->execute())->toThrow(UniqueConstraintViolationException::class);
});
