<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Marketing\Docs\Api\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MealsControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_meals_module_api_docs_page(): void
    {

        $response = $this->get(route('marketing.docs.api.modules.meals', absolute: false));

        $response->assertOk();
        $response->assertViewIs('marketing.docs.api.modules.meals');

    }
}
