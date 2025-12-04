<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

final class MarketingLayout extends Component
{
    public function render(): View
    {
        return view('layouts.marketing');
    }
}
