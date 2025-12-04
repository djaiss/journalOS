<?php

declare(strict_types=1);

namespace Tests\Unit\Jobs;

use App\Jobs\UpdateUserLastActivityDate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class UpdateUserLastActivityDateTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_updates_user_last_activity_date(): void
    {
        $user = User::factory()->create([
            'last_activity_at' => null,
        ]);

        UpdateUserLastActivityDate::dispatch($user);

        $user->refresh();

        $this->assertNotNull($user->last_activity_at);
        $this->assertEqualsWithDelta(
            now()->timestamp,
            $user->last_activity_at->timestamp,
            1,
        );
    }
}
