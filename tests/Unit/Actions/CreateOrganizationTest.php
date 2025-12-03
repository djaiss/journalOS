<?php

declare(strict_types=1);

use App\Actions\CreateOrganization;
use App\Models\Organization;
use App\Models\User;
use Carbon\Carbon;
use App\Jobs\LogUserAction;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

it('creates an organization', function (): void {
    Queue::fake();
    Carbon::setTestNow(Carbon::parse('2025-03-17 10:00:00'));

    $user = User::factory()->create();

    $organization = (new CreateOrganization(
        user: $user,
        organizationName: 'Dunder Mifflin',
    ))->execute();

    $this->assertDatabaseHas('organizations', [
        'id' => $organization->id,
        'name' => 'Dunder Mifflin',
        'slug' => $organization->id . '-dunder-mifflin',
    ]);

    $this->assertDatabaseHas('organization_user', [
        'organization_id' => $organization->id,
        'user_id' => $user->id,
        'joined_at' => '2025-03-17 10:00:00',
    ]);

    expect($organization)->toBeInstanceOf(Organization::class);

    Queue::assertPushedOn(
        queue: 'low',
        job: LogUserAction::class,
        callback: function (LogUserAction $job) use ($user): bool {
            return $job->action === 'organization_creation' && $job->user->id === $user->id;
        },
    );
});

it('throws an exception if organization name contains special characters', function (): void {
    $this->expectException(ValidationException::class);

    $user = User::factory()->create();

    (new CreateOrganization(
        user: $user,
        organizationName: 'Dunder@ / Mifflin!',
    ))->execute();
});
