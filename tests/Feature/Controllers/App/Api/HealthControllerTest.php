<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\App\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class HealthControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_checks_the_health_of_the_application(): void
    {
        $response = $this->json('GET', '/api/health');

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'ok',
            'status' => 200,
        ]);
    }
}
