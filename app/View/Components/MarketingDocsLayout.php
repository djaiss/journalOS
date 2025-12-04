<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

final class MarketingDocsLayout extends Component
{
    public function __construct(
        public array $breadcrumbItems = [],
    ) {}

    public function render(): View
    {
        return view('layouts.docs', [
            'breadcrumbItems' => $this->breadcrumbItems,
        ]);
    }
}
