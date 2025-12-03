<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\CreateOrganization;
use App\Jobs\LogUserAction;
use App\Models\Organization;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateOrganizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_an_organization(): void
    {
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

        $this->assertInstanceOf(Organization::class, $organization);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === 'organization_creation' && $job->user->id === $user->id;
            },
        );
    }

    public function test_it_throws_an_exception_if_organization_name_contains_special_characters(): void
    {
        $this->expectException(ValidationException::class);

        $user = User::factory()->create();

        (new CreateOrganization(
            user: $user,
            organizationName: 'Dunder@ / Mifflin!',
        ))->execute();
    }
}
