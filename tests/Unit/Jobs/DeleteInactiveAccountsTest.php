<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\DeleteInactiveAccounts;
use App\Mail\AccountAutomaticallyDestroyed;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class DeleteInactiveAccountsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_inactive_accounts_with_auto_delete_enabled(): void
    {
        Mail::fake();
        config(['app.account_deletion_notification_email' => 'admin@journalos.cloud']);

        // Create an inactive user with auto_delete_account enabled
        $inactiveUser = User::factory()->create([
            'auto_delete_account' => true,
            'last_activity_at' => now()->subMonths(7),
        ]);

        // Create an inactive user without auto_delete_account enabled
        $inactiveUserNoDelete = User::factory()->create([
            'auto_delete_account' => false,
            'last_activity_at' => now()->subMonths(7),
        ]);

        // Create an active user with auto_delete_account enabled
        $activeUser = User::factory()->create([
            'auto_delete_account' => true,
            'last_activity_at' => now()->subMonths(3),
        ]);

        (new DeleteInactiveAccounts())->handle();

        $this->assertDatabaseMissing('users', [
            'id' => $inactiveUser->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $inactiveUserNoDelete->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $activeUser->id,
        ]);

        Mail::assertQueued(AccountAutomaticallyDestroyed::class, function (AccountAutomaticallyDestroyed $job): bool {
            return $job->to[0]['address'] === 'admin@journalos.cloud';
        });
    }

    #[Test]
    public function it_does_nothing_when_no_inactive_users_with_auto_delete_exist(): void
    {
        Mail::fake();

        // Create only active users
        User::factory()->create([
            'auto_delete_account' => true,
            'last_activity_at' => now()->subMonths(3),
        ]);

        (new DeleteInactiveAccounts())->handle();

        Mail::assertNotQueued(AccountAutomaticallyDestroyed::class);
    }
}
