<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\GenerateOrganizationAvatar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GenerateOrganizationAvatarTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_generates_a_base64_encoded_svg_avatar(): void
    {
        $generator = new GenerateOrganizationAvatar(
            seed: 'test-seed',
        );
        $result = $generator->execute();

        $this->assertStringStartsWith('data:image/svg+xml;base64,', $result);
        $this->assertStringContainsString('data:image/svg+xml;base64,', $result);

        // Decode and verify it's valid SVG
        $base64Part = mb_substr($result, mb_strlen('data:image/svg+xml;base64,'));
        $decodedSvg = base64_decode($base64Part);

        $this->assertStringContainsString('<svg', $decodedSvg);
        $this->assertStringContainsString('</svg>', $decodedSvg);
        $this->assertStringContainsString('viewBox="0 0 120 120"', $decodedSvg);
        $this->assertStringContainsString('<circle', $decodedSvg);
        $this->assertStringContainsString('linearGradient', $decodedSvg);
    }
}
