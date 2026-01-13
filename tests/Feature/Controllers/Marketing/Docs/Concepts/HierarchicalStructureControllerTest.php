<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Marketing\Docs\Concepts;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class HierarchicalStructureControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_hierarchical_structure_concepts_page(): void
    {

        $response = $this->get(route('marketing.docs.concepts.hierarchical-structure', absolute: false));

        $response->assertOk();
        $response->assertViewIs('marketing.docs.concepts.hierarchy-structure');

    }
}
