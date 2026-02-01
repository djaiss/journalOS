<?php

declare(strict_types = 1);

namespace Tests\Feature\Controllers\Marketing\Company\Handbook;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class HandbookPrinciplesControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_handbook_principles_page(): void
    {
        $response = $this->get(route('marketing.company.handbook.principles', absolute: false));

        $response->assertOk();
        $response->assertViewIs('marketing.company.handbook.principles');
    }
}
