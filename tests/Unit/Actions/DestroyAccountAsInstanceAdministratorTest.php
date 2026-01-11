<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Models\User;
use App\Actions\DestroyAccountAsInstanceAdministrator;
use Exception;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class DestroyAccountAsInstanceAdministratorTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_destroys_an_account_as_instance_administrator(): void
    {
        $account = User::factory()->create();
        $user = User::factory()->create([
            'is_instance_admin' => true,
        ]);

        (new DestroyAccountAsInstanceAdministrator(
            user: $user,
            account: $account,
        ))->execute();

        $this->assertDatabaseMissing('users', [
            'id' => $account->id,
        ]);
    }

    #[Test]
    public function it_fails_if_user_is_not_instance_administrator(): void
    {
        $account = User::factory()->create();
        $user = User::factory()->create([
            'is_instance_admin' => false,
        ]);

        $this->expectException(Exception::class);

        (new DestroyAccountAsInstanceAdministrator(
            user: $user,
            account: $account,
        ))->execute();
    }
}
