<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

final class MarketingHandbookLayout extends Component
{
    public function __construct(
        public array $breadcrumbItems = [],
    ) {}

    public function render(): View
    {
        return view('layouts.handbook', [
            'breadcrumbItems' => $this->breadcrumbItems,
        ]);
    }
}
