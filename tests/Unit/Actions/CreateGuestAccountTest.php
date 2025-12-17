<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreateGuestAccount;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\Journal;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class CreateGuestAccountTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_creates_a_guest_account(): void
    {
        Queue::fake();
        Carbon::setTestNow(Carbon::create(2025, 12, 16));

        $user = (new CreateGuestAccount())->execute();

        $this->assertInstanceOf(
            User::class,
            $user,
        );

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_guest' => true,
        ]);

        $this->assertEquals('James', $user->first_name);
        $this->assertEquals('Bond', $user->last_name);
        $this->assertTrue($user->is_guest);
        $this->assertNotNull($user->guest_token);
        $this->assertStringStartsWith('guest+', $user->email);
        $this->assertEquals('2025-12-23 00:00:00', $user->guest_expires_at->format('Y-m-d H:i:s'));

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
    public function it_creates_first_journal_for_guest(): void
    {
        Queue::fake();

        $user = (new CreateGuestAccount())->execute();

        $this->assertDatabaseHas('journals', [
            'user_id' => $user->id,
        ]);

        $journal = Journal::query()->where('user_id', $user->id)->first();

        $this->assertInstanceOf(
            Journal::class,
            $journal,
        );
        $this->assertEquals(
            'My first journal',
            $journal->name,
        );
    }

    #[Test]
    public function it_creates_unique_email_for_each_guest(): void
    {
        Queue::fake();

        $user1 = (new CreateGuestAccount())->execute();
        $user2 = (new CreateGuestAccount())->execute();

        $this->assertNotEquals($user1->email, $user2->email);
        $this->assertStringStartsWith('guest+', $user1->email);
        $this->assertStringStartsWith('guest+', $user2->email);
    }

    #[Test]
    public function it_sets_guest_expiration_to_seven_days(): void
    {
        Queue::fake();
        Carbon::setTestNow(Carbon::create(2025, 12, 16, 14, 30, 0));

        $user = (new CreateGuestAccount())->execute();

        $this->assertEquals(
            Carbon::create(2025, 12, 23, 14, 30, 0),
            $user->guest_expires_at,
        );
    }
}
