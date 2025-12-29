<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Settings;

use App\Models\EmailSent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class EmailSentControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $emailJsonStructure = [
        'data' => [
            'type',
            'id',
            'attributes' => [
                'uuid',
                'email_type',
                'email_address',
                'subject',
                'body',
                'sent_at',
                'delivered_at',
                'bounced_at',
            ],
            'links' => [
                'self',
            ],
        ],
    ];

    private array $emailCollectionStructure = [
        'data' => [
            '*' => [
                'type',
                'id',
                'attributes' => [
                    'uuid',
                    'email_type',
                    'email_address',
                    'subject',
                    'body',
                    'sent_at',
                    'delivered_at',
                    'bounced_at',
                ],
                'links' => [
                    'self',
                ],
            ],
        ],
        'links' => [
            'first',
            'last',
            'prev',
            'next',
        ],
        'meta' => [
            'current_page',
            'from',
            'last_page',
            'path',
            'per_page',
            'to',
            'total',
        ],
    ];

    #[Test]
    public function it_can_get_paginated_emails(): void
    {
        Carbon::setTestNow('2025-02-03 12:00:00');
        $user = User::factory()->create();

        $emails = EmailSent::factory()->count(15)->create([
            'user_id' => $user->id,
            'email_type' => 'daily.summary',
        ]);

        EmailSent::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/settings/emails');

        $response->assertStatus(200);
        $response->assertJsonStructure($this->emailCollectionStructure);
        $response->assertJson([
            'meta' => [
                'current_page' => 1,
                'per_page' => 10,
                'total' => 15,
            ],
        ]);

        $firstEmail = $emails->sortByDesc('sent_at')->first();
        $response->assertJson([
            'data' => [
                [
                    'type' => 'email',
                    'id' => (string) $firstEmail->id,
                    'attributes' => [
                        'uuid' => $firstEmail->uuid,
                        'email_type' => 'daily.summary',
                        'email_address' => $firstEmail->email_address,
                        'subject' => $firstEmail->subject,
                        'body' => $firstEmail->body,
                        'sent_at' => $firstEmail->sent_at?->timestamp,
                        'delivered_at' => $firstEmail->delivered_at?->timestamp,
                        'bounced_at' => $firstEmail->bounced_at?->timestamp,
                    ],
                ],
            ],
        ]);
    }

    #[Test]
    public function it_can_show_a_specific_email(): void
    {
        Carbon::setTestNow('2025-02-03 12:00:00');
        $user = User::factory()->create();
        $email = EmailSent::factory()->create([
            'user_id' => $user->id,
            'email_type' => 'security.alert',
            'subject' => 'Suspicious activity detected',
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', "/api/settings/emails/{$email->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure($this->emailJsonStructure);
        $response->assertJson([
            'data' => [
                'type' => 'email',
                'id' => (string) $email->id,
                'attributes' => [
                    'uuid' => $email->uuid,
                    'email_type' => 'security.alert',
                    'email_address' => $email->email_address,
                    'subject' => 'Suspicious activity detected',
                    'body' => $email->body,
                    'sent_at' => $email->sent_at?->timestamp,
                    'delivered_at' => $email->delivered_at?->timestamp,
                    'bounced_at' => $email->bounced_at?->timestamp,
                ],
            ],
        ]);
    }

    #[Test]
    public function it_returns_403_when_trying_to_access_another_user_email(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $email = EmailSent::factory()->create([
            'user_id' => $otherUser->id,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', "/api/settings/emails/{$email->id}");

        $response->assertForbidden();
        $response->assertJson([
            'message' => 'Unauthorized action.',
        ]);
    }

    #[Test]
    public function it_returns_404_when_email_has_no_user_id(): void
    {
        $user = User::factory()->create();
        $email = EmailSent::factory()->create([
            'user_id' => null,
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', "/api/settings/emails/{$email->id}");

        $response->assertNotFound();
        $response->assertJson([
            'message' => 'Email not found.',
        ]);
    }

    #[Test]
    public function it_returns_401_when_not_authenticated(): void
    {
        $response = $this->json('GET', '/api/settings/emails');
        $response->assertUnauthorized();

        $email = EmailSent::factory()->create();
        $response = $this->json('GET', "/api/settings/emails/{$email->id}");
        $response->assertUnauthorized();
    }
}
