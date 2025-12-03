<?php

declare(strict_types=1);

namespace Tests\Unit\Mail;

use App\Mail\LoginFailed;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class LoginFailedTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_should_have_correct_envelope_subject(): void
    {
        Config::set('app.name', 'OrganizationOS');

        $mailable = new LoginFailed();

        $this->assertEquals('Login attempt on OrganizationOS', $mailable->envelope()->subject);

        $rendered = $mailable->render();

        $this->assertStringContainsString('Login attempt on OrganizationOS', $rendered);
        $this->assertStringContainsString('A user (hopefully it\'s you) tried to login to your account but failed.', $rendered);
        $this->assertStringContainsString('If you did not try to login, make sure to visit your account and change your password, just in case.', $rendered);
        $this->assertStringContainsString('Thanks,', $rendered);
        $this->assertStringContainsString('OrganizationOS', $rendered);
    }
}
