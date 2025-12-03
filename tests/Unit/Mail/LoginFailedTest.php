<?php

declare(strict_types=1);

use App\Mail\LoginFailed;
use Illuminate\Support\Facades\Config;

it('should have correct envelope subject', function (): void {
    Config::set('app.name', 'OrganizationOS');

    $mailable = new LoginFailed();

    expect($mailable->envelope()->subject)->toEqual('Login attempt on OrganizationOS');

    $rendered = $mailable->render();

    $this->assertStringContainsString('Login attempt on OrganizationOS', $rendered);
    $this->assertStringContainsString('A user (hopefully it\'s you) tried to login to your account but failed.', $rendered);
    $this->assertStringContainsString('If you did not try to login, make sure to visit your account and change your password, just in case.', $rendered);
    $this->assertStringContainsString('Thanks,', $rendered);
    $this->assertStringContainsString('OrganizationOS', $rendered);
});
