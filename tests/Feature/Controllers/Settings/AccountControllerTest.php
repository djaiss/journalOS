<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Settings;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_the_user_account(): void
    {
        Carbon::setTestNow(Carbon::create(2018, 1, 1));
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get('/settings/account');

        $response->assertStatus(200);
        $response->assertViewIs('settings.account.index');

        $response = $this->actingAs($user)
            ->delete('/settings/account', [
                'feedback' => 'I want to delete my account',
            ]);

        $response->assertRedirect('/login');
    }
}
