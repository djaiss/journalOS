<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreateAccount;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class CreateAccountTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_an_account(): void
    {
        Queue::fake();
        Carbon::setTestNow(Carbon::create(2018, 1, 1));

        $user = (new CreateAccount(
            email: 'michael.scott@dundermifflin.com',
            password: 'password',
            firstName: 'Michael',
            lastName: 'Scott',
        ))->execute();

        $this->assertInstanceOf(User::class, $user);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

        $this->assertEquals('michael.scott@dundermifflin.com', $user->email);
        $this->assertEquals('Michael', $user->first_name);
        $this->assertEquals('Scott', $user->last_name);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'account_created' && $job->user->id === $user->id;
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
    public function it_cant_create_an_account_with_the_same_email(): void
    {
        User::factory()->create([
            'email' => 'michael.scott@dundermifflin.com',
        ]);

        $this->expectException(UniqueConstraintViolationException::class);

        (new CreateAccount(
            email: 'michael.scott@dundermifflin.com',
            password: 'password',
            firstName: 'Michael',
            lastName: 'Scott',
        ))->execute();
    }

    #[Test]
    public function it_throws_when_email_is_too_long(): void
    {
        $this->expectException(ValidationException::class);

        (new CreateAccount(
            email: str_repeat('a', 256),
            password: 'password',
            firstName: 'Michael',
            lastName: 'Scott',
        ))->execute();
    }
}
