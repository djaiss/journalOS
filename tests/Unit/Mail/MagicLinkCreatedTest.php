<?php

declare(strict_types=1);

namespace Tests\Unit\Mail;

use App\Mail\MagicLinkCreated;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class MagicLinkCreatedTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_should_have_correct_envelope_subject(): void
    {
        Config::set('app.name', 'OrganizationOS');

        $mailable = new MagicLinkCreated('https://example.com/login/abc123');

        $this->assertEquals('Login to OrganizationOS', $mailable->envelope()->subject);
    }

    #[Test]
    public function it_should_render_markdown_content_correctly(): void
    {
        Config::set('app.name', 'OrganizationOS');

        $link = 'https://example.com/login/abc123';
        $mailable = new MagicLinkCreated($link);
        $rendered = $mailable->render();

        $this->assertStringContainsString('Your login link for OrganizationOS', $rendered);
        $this->assertStringContainsString($link, $rendered);
        $this->assertStringContainsString('This link will only be valid for the next 5 minutes.', $rendered);
        $this->assertStringContainsString('If you did not request this link, make sure to visit your account and change your password, just in case.', $rendered);
        $this->assertStringContainsString('Thanks,', $rendered);
        $this->assertStringContainsString('OrganizationOS', $rendered);
    }
}
