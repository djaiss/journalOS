<?php

/**
 * @var string $src
 * @var string $srcset
 * @var string $alt
 * @var int|string $width
 * @var int|string $height
 * @var string $loading
 * @var \Illuminate\View\ComponentAttributeBag $attributes
 */
?>

@props([
  'src',
  'alt',
  'width',
  'height',
  'srcset' => null,
  'loading' => 'lazy',
])

<img src="{{ $src }}" alt="{{ $alt }}" width="{{ $width }}" height="{{ $height }}" @if ($srcset) srcset="{{ $srcset }}" @endif loading="{{ $loading }}" {{ $attributes }} />
