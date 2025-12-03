<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\Generate2faQRCode;
use App\Jobs\LogUserAction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class Generate2faQRCodeTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_generates_a_2fa_qr_code(): void
    {
        Queue::fake();
        Carbon::setTestNow(Carbon::parse('2025-07-16 10:00:00'));

        $user = User::factory()->create([
            'email' => 'michael.scott@dundermifflin.com',
        ]);

        $result = (new Generate2faQRCode(
            user: $user,
        ))->execute();

        $this->assertIsString($result['secret']);

        Queue::assertPushedOn(
            queue: 'low',
            job: LogUserAction::class,
            callback: function (LogUserAction $job) use ($user): bool {
                return $job->action === '2fa_qr_code_generation'
                    && $job->user->id === $user->id
                    && $job->description === 'Generated 2FA QR code for setup';
            },
        );
    }
}
