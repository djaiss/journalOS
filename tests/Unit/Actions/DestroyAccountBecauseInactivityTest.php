<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\DestroyAccountBecauseInactivity;
use App\Mail\AccountAutomaticallyDestroyed;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class DestroyAccountBecauseInactivityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_destroys_an_inactive_account(): void
    {
        Mail::fake();
        config(['journalos.account_deletion_notification_email' => 'admin@journalos.cloud']);

        $user = User::factory()->create([
            'last_activity_at' => now()->subMonths(7),
            'created_at' => now()->subMonths(12),
        ]);

        (new DestroyAccountBecauseInactivity(
            user: $user,
        ))->execute();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);

        Mail::assertQueued(AccountAutomaticallyDestroyed::class, function (AccountAutomaticallyDestroyed $job): bool {
            return $job->to[0]['address'] === 'admin@journalos.cloud';
        });
    }

    #[Test]
    public function it_does_not_destroy_a_recently_active_account(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'last_activity_at' => now()->subMonths(3),
        ]);

        (new DestroyAccountBecauseInactivity(
            user: $user,
        ))->execute();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

        Mail::assertNotQueued(AccountAutomaticallyDestroyed::class);
    }

    #[Test]
    public function it_does_not_destroy_account_without_activity_record(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'last_activity_at' => null,
        ]);

        (new DestroyAccountBecauseInactivity(
            user: $user,
        ))->execute();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);

        Mail::assertNotQueued(AccountAutomaticallyDestroyed::class);
    }
}
