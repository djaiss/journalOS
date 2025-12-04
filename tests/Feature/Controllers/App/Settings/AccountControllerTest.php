<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Settings;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_deletes_the_user_account(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/settings/account');

        $response->assertStatus(200);
        $response->assertViewIs('app.settings.account.index');

        $response = $this->actingAs($user)
            ->delete('/settings/account', [
                'feedback' => 'I want to delete my account',
            ]);

        $response->assertRedirect('/login');
    }
}
