<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Marketing\Docs\Api\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class CognitiveLoadControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_cognitive_load_module_api_docs_page(): void
    {
        $response = $this->get(route('marketing.docs.api.modules.cognitive-load', absolute: false));

        $response->assertOk();
        $response->assertViewIs('marketing.docs.api.modules.cognitive-load');
    }
}
