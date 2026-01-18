<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Marketing\Company\Handbook;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class HandbookWritingControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_handbook_writing_page(): void
    {
        $response = $this->get(route('marketing.company.handbook.marketing.writing', absolute: false));

        $response->assertOk();
        $response->assertViewIs('marketing.company.handbook.writing');
    }
}
