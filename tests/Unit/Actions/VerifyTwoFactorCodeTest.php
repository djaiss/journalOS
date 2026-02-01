<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\VerifyTwoFactorCode;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class VerifyTwoFactorCodeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_verifies_a_rescue_code_and_updates_last_activity(): void
    {
        $user = User::factory()->create([
            'two_factor_recovery_codes' => ['code-one', 'code-two'],
        ]);

        $result = new VerifyTwoFactorCode(
            user: $user,
            code: 'code-one',
        )->execute();

        $this->assertTrue($result);
        $this->assertNotContains('code-one', $user->refresh()->two_factor_recovery_codes);

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: function (UpdateUserLastActivityDate $job) use ($user): bool {
                return $job->user->id === $user->id;
            },
        );
    }

    #[Test]
    public function it_returns_false_and_skips_activity_update_for_invalid_codes(): void
    {
        $user = User::factory()->create([
            'two_factor_recovery_codes' => ['code-one'],
        ]);

        $result = new VerifyTwoFactorCode(
            user: $user,
            code: 'wrong-code',
        )->execute();

        $this->assertFalse($result);
        $this->assertEquals(['code-one'], $user->refresh()->two_factor_recovery_codes);

        Queue::assertNothingPushed();
    }
}
