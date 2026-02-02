<?php

/**
 * @var string|null $status
 * @var \Illuminate\View\ComponentAttributeBag $attributes
 */
?>

@props([
  'status',
])

@if ($status)
  <div {{ $attributes->merge(['class' => 'font-medium text-sm text-green-600']) }}>
    {{ $status }}
  </div>
@endif
