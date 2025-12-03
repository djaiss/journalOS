<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;
use App\Models\Organization;

final class AppLayout extends Component
{
    public function __construct(
        public string $title = '',
        public ?Organization $organization = null,
    ) {}

    public function render(): View
    {
        return view('layouts.app', [
            'organization' => $this->organization,
        ]);
    }
}
