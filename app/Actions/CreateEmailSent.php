<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\EmailSent;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Stevebauman\Purify\Facades\Purify;

final class CreateEmailSent
{
    private EmailSent $emailSent;

    public function __construct(
        public ?Organization $organization,
        public User $user,
        public ?string $uuid,
        public string $emailType,
        public string $emailAddress,
        public string $subject,
        public string $body,
    ) {}

    public function execute(): EmailSent
    {
        $this->validate();
        $this->sanitize();
        $this->create();

        return $this->emailSent;
    }

    private function validate(): void
    {
        if ($this->organization && $this->user->isPartOfOrganization($this->organization) === false) {
            throw new ModelNotFoundException('User is not part of the organization.');
        }
    }

    /**
     * This will remove any links from the body of the email, since they
     * could contain links that are not valid anymore.
     *
     * @return void
     */
    private function sanitize(): void
    {
        $config = ['HTML.ForbiddenElements' => 'a'];
        $this->body = Purify::config($config)->clean($this->body);
    }

    private function create(): void
    {
        $this->emailSent = EmailSent::create([
            'organization_id' => $this->organization instanceof Organization ? $this->organization->id : null,
            'user_id' => $this->user->id,
            'uuid' => $this->uuid,
            'email_type' => $this->emailType,
            'email_address' => $this->emailAddress,
            'subject' => $this->subject,
            'body' => $this->body,
        ]);
    }
}
