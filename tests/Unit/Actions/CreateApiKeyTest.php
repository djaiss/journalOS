<?php

declare(strict_types=1);

use App\Actions\CreateApiKey;
use App\Jobs\LogUserAction;
use App\Jobs\SendEmail;
use App\Models\User;
use Illuminate\Support\Facades\Queue;

it('creates an api key', function (): void {
    Queue::fake();

    $user = User::factory()->create();

    (new CreateApiKey(
        user: $user,
        label: 'Test API Key',
    ))->execute();

    $this->assertDatabaseHas('personal_access_tokens', [
        'name' => 'Test API Key',
        'tokenable_id' => $user->id,
        'tokenable_type' => User::class,
    ]);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: function (LogUserAction $job) use ($user): bool {
            return $job->action === 'api_key_creation' && $job->user->id === $user->id;
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
