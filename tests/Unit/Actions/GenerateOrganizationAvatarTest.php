<?php

declare(strict_types=1);

use App\Actions\GenerateOrganizationAvatar;

it('generates a base64 encoded SVG avatar', function (): void {
    $result = new GenerateOrganizationAvatar(
        seed: 'test-seed',
    )->execute();

    expect($result)->toStartWith('data:image/svg+xml;base64,');
    expect($result)->toContain('data:image/svg+xml;base64,');

    // Decode and verify it's valid SVG
    $base64Part = mb_substr($result, mb_strlen('data:image/svg+xml;base64,'));
    $decodedSvg = base64_decode($base64Part);

    expect($decodedSvg)->toContain('<svg');
    expect($decodedSvg)->toContain('</svg>');
    expect($decodedSvg)->toContain('viewBox="0 0 120 120"');
    expect($decodedSvg)->toContain('<circle');
    expect($decodedSvg)->toContain('linearGradient');
});
