<?php

declare(strict_types=1);

use App\Mail\MagicLinkCreated;
use Illuminate\Support\Facades\Config;

it('should have correct envelope subject', function (): void {
    Config::set('app.name', 'OrganizationOS');

    $mailable = new MagicLinkCreated('https://example.com/login/abc123');

    expect($mailable->envelope()->subject)->toEqual('Login to OrganizationOS');
});

it('should render markdown content correctly', function (): void {
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
});
