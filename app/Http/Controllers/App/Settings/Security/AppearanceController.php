<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings\Security;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class AppearanceController extends Controller
{
    public function edit(): View
    {
        return view('app.settings.appearance');
    }
}
