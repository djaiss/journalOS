<?php

declare(strict_types=1);

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class UserIpAddressChanged extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public User $user,
        public string $ip,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New sign-in detected on your ' . config('app.name') . ' account',
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.account.user-ip-changed-text',
            markdown: 'mail.account.user-ip-changed',
            with: [
                'email' => $this->user->email,
                'ip' => $this->ip,
            ],
        );
    }
}
