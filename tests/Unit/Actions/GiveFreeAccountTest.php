<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Models\User;
use App\Actions\GiveFreeAccount;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class GiveFreeAccountTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    public function it_gives_free_account_to_an_account(): void
    {
        Queue::fake();

        $account = User::factory()->create();
        $user = User::factory()->create([
            'is_instance_admin' => true,
        ]);

        (new GiveFreeAccount(
            user: $user,
            account: $account,
        ))->execute();

        $this->assertDatabaseHas('users', [
            'id' => $account->id,
            'has_lifetime_access' => true,
        ]);
    }

    #[Test]
    public function it_fails_if_user_is_not_instance_administrator(): void
    {
        Queue::fake();

        $account = User::factory()->create();
        $user = User::factory()->create([
            'is_instance_admin' => false,
        ]);

        $this->expectException(Exception::class);

        (new GiveFreeAccount(
            user: $user,
            account: $account,
        ))->execute();
    }
}
