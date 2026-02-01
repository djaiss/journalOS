<?php

declare(strict_types = 1);

namespace Tests\Unit\Actions;

use App\Actions\Generate2faQRCode;
use App\Jobs\LogUserAction;
use App\Jobs\UpdateUserLastActivityDate;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class Generate2faQRCodeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Queue::fake();
    }

    #[Test]
    public function it_generates_a_2fa_qr_code(): void
    {
        Carbon::setTestNow(Carbon::parse('2025-07-16 10:00:00'));

        $user = User::factory()->create([
            'email' => 'michael.scott@dundermifflin.com',
        ]);

        $result = new Generate2faQRCode(
            user: $user,
        )->execute();

        $this->assertIsString($result['secret']);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: fn (LogUserAction $job) => (
                    $job->action === '2fa_qr_code_generation'
                    && $job->user->id === $user->id
                    && $job->description === 'Generated 2FA QR code for setup'
                ),
        );

        Queue::assertPushedOn(
            queue: 'low',
            job: UpdateUserLastActivityDate::class,
            callback: fn (UpdateUserLastActivityDate $job) => $job->user->id === $user->id,
        );
    }
}
