<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Api\Settings\Profile;

use App\Models\Log;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class LogControllerTest extends TestCase
{
    use RefreshDatabase;

    private array $logJsonStructure = [
        'data' => [
            'type',
            'id',
            'attributes' => [
                'action',
                'description',
                'journal_name',
                'created_at',
                'updated_at',
            ],
            'links' => [
                'self',
            ],
        ],
    ];

    private array $logsCollectionStructure = [
        'data' => [
            '*' => [
                'type',
                'id',
                'attributes' => [
                    'action',
                    'description',
                    'journal_name',
                    'created_at',
                    'updated_at',
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
    public function it_can_get_paginated_logs(): void
    {
        Carbon::setTestNow('2025-01-15 10:00:00');
        $user = User::factory()->create();

        $logs = Log::factory()->count(15)->create([
            'user_id' => $user->id,
            'journal_name' => 'Dunder Mifflin journal',
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', '/api/settings/logs');

        $response->assertStatus(200);
        $response->assertJsonStructure($this->logsCollectionStructure);

        $response->assertJson([
            'meta' => [
                'current_page' => 1,
                'per_page' => 10,
                'total' => 15,
            ],
        ]);

        $firstLog = $logs->sortByDesc('created_at')->sortByDesc('id')->first();
        $response->assertJson([
            'data' => [
                [
                    'type' => 'log',
                    'id' => (string) $firstLog->id,
                    'attributes' => [
                        'action' => $firstLog->action,
                        'description' => $firstLog->description,
                        'journal_name' => 'Dunder Mifflin journal',
                        'created_at' => $firstLog->created_at->timestamp,
                        'updated_at' => $firstLog->created_at->timestamp,
                    ],
                ],
            ],
        ]);
    }

    #[Test]
    public function it_can_show_a_specific_log(): void
    {
        Carbon::setTestNow('2025-01-15 10:00:00');
        $user = User::factory()->create();
        $log = Log::factory()->create([
            'user_id' => $user->id,
            'action' => 'user.login',
            'description' => 'User logged in successfully',
            'journal_name' => 'Dunder Mifflin journal',
        ]);

        Sanctum::actingAs($user);

        $response = $this->json('GET', "/api/settings/logs/{$log->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure($this->logJsonStructure);

        $response->assertJson([
            'data' => [
                'type' => 'log',
                'id' => (string) $log->id,
                'attributes' => [
                    'action' => 'user.login',
                    'description' => 'User logged in successfully',
                    'journal_name' => 'Dunder Mifflin journal',
                    'created_at' => $log->created_at->timestamp,
                    'updated_at' => $log->created_at->timestamp,
                ],
            ],
        ]);
    }

    #[Test]
    public function it_returns_403_when_trying_to_access_another_user_log(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $log = Log::factory()->create([
            'user_id' => $user2->id,
            'journal_name' => 'Dunder Mifflin journal',
        ]);

        Sanctum::actingAs($user1);

        $response = $this->json('GET', "/api/settings/logs/{$log->id}");

        $response->assertForbidden();
        $response->assertJson([
            'message' => 'Unauthorized action.',
        ]);
    }

    #[Test]
    public function it_returns_404_when_log_has_no_journal_id(): void
    {
        $user = User::factory()->create();
        $log = Log::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->json('GET', "/api/settings/logs/{$log->id}");

        $response->assertStatus(403);
        $response->assertJson([
            'message' => 'Unauthorized action.',
        ]);
    }

    #[Test]
    public function it_returns_401_when_not_authenticated(): void
    {
        $response = $this->json('GET', '/api/settings/logs');
        $response->assertUnauthorized();

        $log = Log::factory()->create();
        $response = $this->json('GET', "/api/settings/logs/{$log->id}");
        $response->assertUnauthorized();
    }
}
