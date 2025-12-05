<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

final class AccountAutomaticallyDestroyed extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $age,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account automatically deleted',
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.account.automatically-destroyed-text',
            markdown: 'mail.account.automatically-destroyed',
            with: [
                'age' => $this->age,
            ],
        );
    }
}
