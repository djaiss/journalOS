<?php

declare(strict_types=1);

namespace Tests\Unit\Actions;

use App\Actions\GenerateJournalAvatar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

final class GenerateJournalAvatarTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_generates_a_base64_encoded_svg_avatar(): void
    {
        Queue::fake();

        $generator = new GenerateJournalAvatar(
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
