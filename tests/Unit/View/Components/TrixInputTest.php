<?php

declare(strict_types = 1);

namespace Tests\Unit\View\Components;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class TrixInputTest extends TestCase
{
    #[Test]
    public function it_renders_without_file_upload_controls(): void
    {
        $html = view('components.trix-input', [
            'id' => 'notes',
            'name' => 'notes',
        ])->render();

        $this->assertStringNotContainsString('data-trix-button-group="file-tools"', $html);
        $this->assertStringNotContainsString('trix-button--icon-attach', $html);
    }
}
