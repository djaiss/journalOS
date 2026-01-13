<?php

declare(strict_types=1);

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
        $response->assertSeeText('A simple diary to track the shape of your days.');
        $response->assertSeeText('JournalOS helps you log sleep, mood, health, work, and more without writing long essays.');
        $response->assertSeeText('Daily logging');
        $response->assertSeeText('Monthly and yearly stats');
        $response->assertSeeText('Random memories');
        $response->assertSeeText('English and French');
    }
}
