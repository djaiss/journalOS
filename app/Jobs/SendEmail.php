<?php

declare(strict_types=1);

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Mail\Mailable;
use App\Enums\EmailType;
use App\Models\User;
use App\Mail\ApiKeyCreated;
use App\Mail\ApiKeyDestroyed;
use App\Mail\MagicLinkCreated;
use App\Mail\LoginFailed;
use Resend\Laravel\Facades\Resend;
use Illuminate\Support\Facades\Mail;
use App\Actions\CreateEmailSent;
use App\Mail\AccountDestroyed;
use App\Mail\UserIpAddressChanged;

final class SendEmail implements ShouldQueue
{
    use Queueable;

    /** @var ApiKeyCreated|ApiKeyDestroyed|MagicLinkCreated|LoginFailed|AccountDestroyed|UserIpChanged */
    private Mailable $mailable;

    private string $subject;

    private ?string $uuid = null;

    /**
     * Send an email to the given user.
     * We need to use this abstraction because for our own use case in production,
     * we use Resend and all its capabilities (including webhooks), so we need
     * to capture the UUID Resend sends.
     * In any other context, the default Laravel Mail class is used, allowing
     * you to send emails the way Laravel Mail does.
     */
    public function __construct(
        public EmailType $emailType,
        public User $user,
        public array $parameters = [],
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->setMailable();
        $this->setSubject();

        if (config('memoir.use_resend')) {
            $this->sendWithResend();
        } else {
            $this->sendTheTraditionalWay();
        }

        $this->recordEmailSent();
    }

    private function setMailable(): void
    {
        $this->mailable = match ($this->emailType) {
            EmailType::API_CREATED => new ApiKeyCreated($this->parameters['label']),
            EmailType::API_DESTROYED => new ApiKeyDestroyed($this->parameters['label']),
            EmailType::MAGIC_LINK_CREATED => new MagicLinkCreated($this->parameters['link']),
            EmailType::LOGIN_FAILED => new LoginFailed(),
            EmailType::ACCOUNT_DESTROYED => new AccountDestroyed(
                reason: $this->parameters['reason'],
                activeSince: $this->parameters['activeSince'],
            ),
            EmailType::USER_IP_CHANGED => new UserIpAddressChanged(
                user: $this->user,
                ip: $this->parameters['ip'],
                browser: $this->parameters['browser'],
                os: $this->parameters['os'],
                device: $this->parameters['device'],
            ),
        };
    }

    private function setSubject(): void
    {
        $this->subject = $this->mailable->envelope()->subject;
    }

    private function sendWithResend(): void
    {
        $response = Resend::emails()->send([
            'from' => config('mail.from.address'),
            'to' => [$this->user->email],
            'subject' => $this->subject,
            'html' => $this->mailable->render(),
        ]);

        $this->uuid = $response->id;
    }

    private function sendTheTraditionalWay(): void
    {
        Mail::to($this->user->email)->send($this->mailable);
    }

    private function recordEmailSent(): void
    {
        new CreateEmailSent(
            user: $this->user,
            uuid: $this->uuid,
            emailType: $this->emailType->value,
            emailAddress: $this->user->email,
            subject: $this->subject,
            body: $this->mailable->render(),
        )->execute();
    }
}
