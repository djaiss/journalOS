<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Settings\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class RecoveryCodeControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_shows_the_recovery_codes(): void
    {
        $user = User::factory()->create([
            'two_factor_recovery_codes' => ['code1', 'code2', 'code3'],
        ]);

        $response = $this->actingAs($user)
            ->get('/settings/security/recovery-codes');

        $response->assertOk();
    }
}
