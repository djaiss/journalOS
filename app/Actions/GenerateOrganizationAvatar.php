<?php

declare(strict_types=1);

namespace App\Actions;

/**
 * Generate an organization avatar.
 */
final class GenerateOrganizationAvatar
{
    public function __construct(
        public string $seed,
    ) {}

    public function execute(): string
    {
        $avatar = $this->generate();

        return 'data:image/svg+xml;base64,' . base64_encode($avatar);
    }

    private function generate(): string
    {
        $hash = hash('sha256', $this->seed);
        $vibes = [
            'sunset' => ['#ff6a00', '#ee0979'],
            'ocean' => ['#2193b0', '#6dd5ed'],
            'forest' => ['#56ab2f', '#a8e063'],
            'fire' => ['#ff4e50', '#f9ca24'],
            'bubble' => ['#ff9a9e', '#fecfef'],
            'daybreak' => ['#ffecd2', '#fcb69f'],
            'crystal' => ['#a8edea', '#fed6e3'],
            'ice' => ['#d299c2', '#fef9d7'],
            'stealth' => ['#434343', '#000000'],
            'purple_haze' => ['#667eea', '#764ba2'],
            'electric' => ['#00d2ff', '#3a7bd5'],
            'coral' => ['#ff7b7b', '#ff416c'],
            'mint' => ['#00b09b', '#96c93d'],
            'gold' => ['#f7971e', '#ffd200'],
            'berry' => ['#8360c3', '#2ebf91'],
            'neon' => ['#ff0099', '#493240'],
            'tropical' => ['#ff9068', '#fd746c'],
            'lavender' => ['#a18cd1', '#fbc2eb'],
            'emerald' => ['#11998e', '#38ef7d'],
            'crimson' => ['#eb3349', '#f45c43'],
            'azure' => ['#667db6', '#0082c8'],
            'amber' => ['#ff8008', '#ffc837'],
            'violet' => ['#da22ff', '#9733ee'],
            'teal' => ['#38ef7d', '#11998e'],
            'rose' => ['#f093fb', '#f5576c'],
            'lime' => ['#c3ec52', '#0ba360'],
            'magenta' => ['#ff0844', '#ffb199'],
            'cyan' => ['#00c6ff', '#0072ff'],
            'peach' => ['#ffcc70', '#ff6b6b'],
            'indigo' => ['#667eea', '#764ba2'],
        ];

        // Pick vibe deterministically
        $index = hexdec(mb_substr($hash, 0, 2)) % count($vibes);
        $chosen = array_values($vibes)[$index];

        return <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120">
          <defs>
            <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
              <stop offset="0%" stop-color="{$chosen[0]}"/>
              <stop offset="100%" stop-color="{$chosen[1]}"/>
            </linearGradient>
          </defs>
          <circle cx="60" cy="60" r="60" fill="url(#grad)" />
        </svg>
        SVG;
    }
}
