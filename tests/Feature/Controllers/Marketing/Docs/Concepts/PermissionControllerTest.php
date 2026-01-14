<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Marketing\Docs\Concepts;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_permissions_concepts_page(): void
    {

        $response = $this->get(route('marketing.docs.concepts.permissions', absolute: false));

        $response->assertOk();
        $response->assertViewIs('marketing.docs.concepts.permissions');

    }
}
