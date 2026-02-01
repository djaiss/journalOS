<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Marketing;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MarketingControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_marketing_homepage(): void
    {
        $response = $this->get(route('marketing.index', absolute: false));

        $response->assertOk();
        $response->assertViewIs('marketing.index');
        $response->assertDontSee('<form', false);
    }
}
