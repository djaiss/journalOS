<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_profile_page(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/settings/profile')->assertOk();
    }

    #[Test]
    public function it_updates_the_profile_information(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put('/settings/profile', [
                'first_name' => 'Michael',
                'last_name' => 'Scott',
                'nickname' => 'Michael',
                'email' => 'michael.scott@dundermifflin.com',
                'locale' => 'en',
                'time_format_24h' => 'true',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/settings/profile');

        $user->refresh();

        $this->assertEquals('Michael', $user->first_name);
        $this->assertEquals('Scott', $user->last_name);
        $this->assertEquals('Michael', $user->nickname);
        $this->assertEquals('michael.scott@dundermifflin.com', $user->email);
        $this->assertEquals('en', $user->locale);
        $this->assertNull($user->email_verified_at);
        $this->assertTrue($user->time_format_24h);
    }

    #[Test]
    public function it_does_not_change_the_email_verification_status_when_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put('/settings/profile', [
                'first_name' => 'Michael',
                'last_name' => 'Scott',
                'nickname' => 'Michael',
                'email' => $user->email,
                'locale' => 'en',
                'time_format_24h' => 'true',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/settings/profile');

        $response->assertSessionHas('status', 'Changes saved');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    #[Test]
    public function it_shows_the_latest_logs(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->put('/settings/profile', [
                'first_name' => 'Michael',
                'last_name' => 'Scott',
                'nickname' => 'Michael',
                'email' => $user->email,
                'locale' => 'en',
                'time_format_24h' => 'true',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/settings/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }
}
