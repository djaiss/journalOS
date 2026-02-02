<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Marketing\Docs\Api\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class SocialEventsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_displays_the_social_events_documentation_page(): void
    {
        $response = $this->get(route('marketing.docs.api.modules.social-events', absolute: false));

        $response->assertOk();
        $response->assertViewIs('marketing.docs.api.modules.social-events');
    }
}
