<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreateEmailSent;
use App\Models\EmailSent;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateEmailSentTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_email_sent(): void
    {
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

        $this->assertEquals(36, mb_strlen($emailSent->uuid));

        $this->assertInstanceOf(EmailSent::class, $emailSent);
    }

    public function test_it_sanitizes_the_body_and_strips_any_links(): void
    {
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
    }

    public function test_it_fails_if_user_doesnt_belong_to_organization(): void
    {
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
    }

    public function test_it_creates_an_email_sent_with_a_uuid(): void
    {
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
    }
}
