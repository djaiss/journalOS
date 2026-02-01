<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Api\Settings\Account;

use App\Mail\AccountDestroyed;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class DestroyAccountControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_the_authenticated_users_account(): void
    {
        Mail::fake();
        config(['app.account_deletion_notification_email' => 'regis@journalos.cloud']);

        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        Sanctum::actingAs($user);

        $reason = 'I no longer need the <b>account</b>';

        $response = $this->json('DELETE', '/api/settings/account', [
            'reason' => $reason,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'The account has been deleted',
            'status' => 200,
        ]);

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $otherUser->id,
        ]);

        $this->assertDatabaseHas('account_deletion_reasons', [
            'reason' => 'I no longer need the account',
        ]);

        Mail::assertQueued(AccountDestroyed::class, fn (AccountDestroyed $job): bool => (
                $job->reason === 'I no longer need the account'
                && $job->to[0]['address'] === 'regis@journalos.cloud'
            ));
    }

    #[Test]
    public function it_requires_authentication(): void
    {
        $response = $this->json('DELETE', '/api/settings/account', [
            'reason' => 'No longer needed',
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function it_requires_a_reason_for_deleting_the_account(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('DELETE', '/api/settings/account');

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['reason']);
    }
}
