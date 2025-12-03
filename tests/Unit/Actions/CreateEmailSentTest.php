<?php

declare(strict_types=1);

use App\Models\EmailSent;
use App\Models\User;
use App\Actions\CreateEmailSent;
use Illuminate\Support\Str;
use App\Models\Organization;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Queue;

it('creates an email sent', function (): void {
    Queue::fake();

    $user = User::factory()->create();

    $emailSent = (new CreateEmailSent(
        user: $user,
        organization: null,
        uuid: 'd27cee22-b10f-46c4-a7dc-af3b46820d80',
        emailType: 'birthday_wishes',
        emailAddress: 'dwight.schrute@dundermifflin.com',
        subject: 'Happy Birthday!',
        body: 'Hope you have a great day!',
    ))->execute();

    $this->assertDatabaseHas('emails_sent', [
        'id' => $emailSent->id,
        'organization_id' => null,
        'user_id' => $user->id,
        'uuid' => 'd27cee22-b10f-46c4-a7dc-af3b46820d80',
        'email_type' => 'birthday_wishes',
        'email_address' => 'dwight.schrute@dundermifflin.com',
        'subject' => 'Happy Birthday!',
        'body' => 'Hope you have a great day!',
    ]);

    expect(mb_strlen($emailSent->uuid))->toEqual(36);

    expect($emailSent)->toBeInstanceOf(EmailSent::class);
});

it('sanitizes the body and strips any links', function (): void {
    Queue::fake();

    $user = User::factory()->create();

    $emailSent = (new CreateEmailSent(
        user: $user,
        organization: null,
        uuid: null,
        emailType: 'birthday_wishes',
        emailAddress: 'dwight.schrute@dundermifflin.com',
        subject: 'Happy Birthday!',
        body: 'Hope you <a href="https://example.com">have a great day!</a>',
    ))->execute();

    $this->assertDatabaseHas('emails_sent', [
        'id' => $emailSent->id,
        'body' => 'Hope you have a great day!',
    ]);
});

it('fails if user doesnt belong to organization', function (): void {
    $this->expectException(ModelNotFoundException::class);

    $user = User::factory()->create();
    $organization = Organization::factory()->create();

    (new CreateEmailSent(
        user: $user,
        organization: $organization,
        uuid: null,
        emailType: 'birthday_wishes',
        emailAddress: 'monica.geller@friends.com',
        subject: 'Happy Birthday!',
        body: 'Hope you have a great day!',
    ))->execute();
});

it('creates an email sent with a uuid', function (): void {
    Queue::fake();

    $user = User::factory()->create();
    $uuid = Str::uuid();

    $emailSent = (new CreateEmailSent(
        user: $user,
        organization: null,
        uuid: $uuid->toString(),
        emailType: 'birthday_wishes',
        emailAddress: 'dwight.schrute@dundermifflin.com',
        subject: 'Happy Birthday!',
        body: 'Hope you have a great day!',
    ))->execute();

    $this->assertDatabaseHas('emails_sent', [
        'id' => $emailSent->id,
        'uuid' => $uuid->toString(),
    ]);
});
