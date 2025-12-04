<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class SendMagicLinkControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_the_magic_link_request_form(): void
    {
        $response = $this->get(route('magic.link'));

        $response->assertStatus(200);
        $response->assertViewIs('app.auth.request-magic-link');
    }

    #[Test]
    public function it_sends_magic_link_when_user_exists(): void
    {
        User::factory()->create([
            'email' => 'michael.scott@dundermifflin.com',
        ]);

        $response = $this->json('POST', route('magic.link.store'), [
            'email' => 'michael.scott@dundermifflin.com',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('app.auth.magic-link-sent');
    }

    #[Test]
    public function it_shows_success_view_even_when_user_not_found(): void
    {
        $response = $this->json('POST', route('magic.link.store'), [
            'email' => 'not.found@dundermifflin.com',
        ]);

        $response->assertStatus(200);
        $response->assertViewIs('app.auth.magic-link-sent');
    }

    #[Test]
    public function it_validates_email_presence(): void
    {
        $response = $this->json('POST', route('magic.link.store'), [
            'email' => '',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    #[Test]
    public function it_validates_email_format(): void
    {
        $response = $this->json('POST', route('magic.link.store'), [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }
}
