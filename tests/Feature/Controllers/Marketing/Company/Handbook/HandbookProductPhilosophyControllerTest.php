<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Marketing\Company\Handbook;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class HandbookProductPhilosophyControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_handbook_product_philosophy_page(): void
    {
        $response = $this->get(route('marketing.company.handbook.marketing.product-philosophy', absolute: false));

        $response->assertOk();
        $response->assertViewIs('marketing.company.handbook.product-philosophy');
    }
}
