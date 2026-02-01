<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\App\Settings;

use App\Mail\AccountDestroyed;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_the_user_account(): void
    {
        Mail::fake();
        config(['app.account_deletion_notification_email' => 'regis@journalos.cloud']);

        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/settings/account');

        $response->assertStatus(200);
        $response->assertViewIs('app.settings.account.index');

        $response = $this->actingAs($user)
            ->delete('/settings/account', [
                'feedback' => 'I want to delete my <b>account</b>',
            ]);

        $response->assertRedirect('/login');

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);

        $this->assertDatabaseHas('account_deletion_reasons', [
            'reason' => 'I want to delete my account',
        ]);

        Mail::assertQueued(AccountDestroyed::class, fn (AccountDestroyed $job): bool => $job->reason === 'I want to delete my account' && $job->to[0]['address'] === 'regis@journalos.cloud');
    }
}
