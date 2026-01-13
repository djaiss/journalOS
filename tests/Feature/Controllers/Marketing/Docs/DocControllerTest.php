<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Marketing\Docs;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class DocControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_renders_the_docs_index(): void
    {

        $response = $this->get(route('marketing.docs.index', absolute: false));

        $response->assertOk();
        $response->assertViewIs('marketing.docs.api.introduction');

    }
}
