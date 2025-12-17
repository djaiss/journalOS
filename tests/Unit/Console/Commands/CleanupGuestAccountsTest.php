<?php

declare(strict_types=1);

namespace Tests\Unit\Console\Commands;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class CleanupGuestAccountsTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_expired_guest_accounts(): void
    {
        // Create an expired guest account
        $expiredGuest = User::factory()->create([
            'is_guest' => true,
            'guest_expires_at' => now()->subDay(),
        ]);

        // Create a non-expired guest account
        $activeGuest = User::factory()->create([
            'is_guest' => true,
            'guest_expires_at' => now()->addDay(),
        ]);

        // Create a regular user (not a guest)
        $regularUser = User::factory()->create([
            'is_guest' => false,
            'guest_expires_at' => null,
        ]);

        $this->artisan('journalos:cleanup-guest-accounts')
            ->assertSuccessful();

        $this->assertDatabaseMissing('users', [
            'id' => $expiredGuest->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $activeGuest->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $regularUser->id,
        ]);
    }

    #[Test]
    public function it_does_nothing_when_no_expired_guest_accounts_exist(): void
    {
        // Create only active guest accounts
        $activeGuest = User::factory()->create([
            'is_guest' => true,
            'guest_expires_at' => now()->addWeek(),
        ]);

        // Create regular users
        $regularUser = User::factory()->create([
            'is_guest' => false,
            'guest_expires_at' => null,
        ]);

        $this->artisan('journalos:cleanup-guest-accounts')
            ->assertSuccessful();

        // Verify no users were deleted
        $this->assertDatabaseHas('users', [
            'id' => $activeGuest->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $regularUser->id,
        ]);
    }

    #[Test]
    public function it_only_deletes_guest_accounts_not_regular_users(): void
    {
        // Create a regular user with a past guest_expires_at date (shouldn't be deleted)
        $regularUser = User::factory()->create([
            'is_guest' => false,
            'guest_expires_at' => now()->subMonth(),
        ]);

        // Create an expired guest account (should be deleted)
        $expiredGuest = User::factory()->create([
            'is_guest' => true,
            'guest_expires_at' => now()->subDay(),
        ]);

        $this->artisan('journalos:cleanup-guest-accounts')
            ->assertSuccessful();

        // Regular user should still exist even with past expiration date
        $this->assertDatabaseHas('users', [
            'id' => $regularUser->id,
        ]);

        // Guest account should be deleted
        $this->assertDatabaseMissing('users', [
            'id' => $expiredGuest->id,
        ]);
    }
}
