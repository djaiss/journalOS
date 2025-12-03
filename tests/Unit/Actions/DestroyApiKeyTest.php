<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\DestroyApiKey;
use App\Jobs\LogUserAction;
use App\Jobs\SendEmail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class DestroyApiKeyTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_an_api_key(): void
    {
        Queue::fake();

        $user = User::factory()->create();
        $user->createToken('Test API Key');

        $tokenId = $user->tokens()->first()->id;

        (new DestroyApiKey(
            user: $user,
            tokenId: $tokenId,
        ))->execute();

        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId,
        ]);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'api_key_deletion' && $job->user->id === $user->id;
            },
        );

        Queue::assertPushedOn(
            queue: 'high',
            job: SendEmail::class,
            callback: function (SendEmail $job) use ($user): bool {
                return $job->user === $user && $job->parameters['label'] === 'Test API Key';
            },
        );
    }
}
