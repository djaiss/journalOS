<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

final class LoginFailed extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct() {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Login attempt on ' . config('app.name'),
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'mail.auth.login-failed-text',
            markdown: 'mail.auth.login-failed',
        );
    }
}
