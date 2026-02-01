<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Marketing\Docs\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ApiIntroductionControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_api_introduction_page(): void
    {
        $response = $this->get(route('marketing.docs.api.index', absolute: false));

        $response->assertOk();
        $response->assertViewIs('marketing.docs.api.introduction');
    }
}
