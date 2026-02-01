<?php

declare(strict_types = 1);

namespace App\Jobs;

use App\Enums\EmailType;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Check last login of the user and compare important login information.
 * If those login information change, send an email to the user to warn him.
 */
final class CheckLastLogin implements ShouldQueue
{
    use Queueable;

    private ?string $oldIp = null;
    private bool $hasChanged = false;

    public function __construct(
        public User $user,
        public string $ip,
    ) {}

    public function handle(): void
    {
        $this->initialize();
        $this->compare();

        if ($this->hasChanged) {
            $this->sendEmail();
        }
    }

    private function initialize(): void
    {
        $this->oldIp = $this->user->last_used_ip ?: null;
    }

    private function compare(): void
    {
        $newIp = $this->ip;

        if ($this->oldIp !== null && $this->oldIp !== $newIp) {
            $this->hasChanged = true;
        }

        $this->user->last_used_ip = $newIp;
        $this->user->save();
    }

    private function sendEmail(): void
    {
        SendEmail::dispatch(
            emailType: EmailType::USER_IP_CHANGED,
            user: $this->user,
            parameters: [
                'ip' => $this->ip,
            ],
        )->onQueue('high');
    }
}
