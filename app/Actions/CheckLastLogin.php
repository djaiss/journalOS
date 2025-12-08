<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\EmailType;
use App\Jobs\LogUserAction;
use App\Jobs\SendEmail;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\User;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

/**
 * Check last login of the user and compare important login information.
 * If those login information change, send an email to the user to warn him.
 */
final class CheckLastLogin
{
    private ?string $oldIp;
    private ?string $oldBrowser;
    private ?string $oldOs;
    private ?string $oldDevice;
    private bool $hasChanged = false;

    public function __construct(
        private readonly User $user,
        private readonly Request $request,
    ) {}

    public function execute(): void
    {
        $this->initialize();
        $this->compare();

        if ($this->hasChanged) {
            $this->sendEmail();
        }
    }

    private function initialize(): void
    {
        $this->oldIp = $this->user?->last_used_ip;
        $this->oldBrowser = $this->user?->last_used_browser;
        $this->oldOs = $this->user?->last_used_os;
        $this->oldDevice = $this->user?->last_used_device;
    }

    private function compare(): void
    {
        $newIp = $this->request->ip();
        $agent = new Agent();

        // Retrieve browser info
        $browser = $agent->browser();
        $browserVersion = $agent->version($browser);
        $browserInfo = "{$browser} {$browserVersion}"; // e.g., "Firefox 145.0"

        // Retrieve OS info
        $os = $agent->platform();
        $osVersion = $agent->version($os);
        $osInfo = "{$os} {$osVersion}"; // e.g., "macOS 10.15"

        // Retrieve device info
        $device = $agent->device(); // e.g., 'Mac', 'iPhone', 'Desktop'

        if ($this->oldIp !== null && $this->oldIp !== $newIp) {
            $this->hasChanged = true;
        }

        if ($this->oldBrowser !== null && $this->oldBrowser !== $browserInfo) {
            $this->hasChanged = true;
        }

        if ($this->oldOs !== null && $this->oldOs !== $osInfo) {
            $this->hasChanged = true;
        }

        if ($this->oldDevice !== null && $this->oldDevice !== $device) {
            $this->hasChanged = true;
        }

        $this->user->last_used_ip = $newIp;
        $this->user->last_used_browser = $browserInfo;
        $this->user->last_used_os = $osInfo;
        $this->user->last_used_device = $device;
        $this->user->save();
        $this->user->refresh();
    }

    private function sendEmail(): void
    {
        SendEmail::dispatch(
            emailType: EmailType::USER_IP_CHANGED,
            user: $this->user,
            parameters: [
                'ip' => $this->user->last_used_ip,
                'browser' => $this->user->last_used_browser,
                'os' => $this->user->last_used_os,
                'device' => $this->user->last_used_device,
            ],
        )->onQueue('high');
    }
}
