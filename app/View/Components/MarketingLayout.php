<?php

declare(strict_types = 1);

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

final class MarketingLayout extends Component
{
    public function __construct(
        public array $breadcrumbItems = [],
    ) {}

    public function render(): View
    {
        return view('layouts.marketing', [
            'breadcrumbItems' => $this->breadcrumbItems,
        ]);
    }
}
