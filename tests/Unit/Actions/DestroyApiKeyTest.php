<?php

declare(strict_types=1);

use App\Jobs\LogUserAction;
use App\Models\User;
use App\Actions\DestroyApiKey;
use App\Jobs\SendEmail;
use Illuminate\Support\Facades\Queue;

it('deletes an api key', function (): void {
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
});
