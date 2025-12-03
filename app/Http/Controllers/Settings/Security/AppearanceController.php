<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings\Security;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

final class AppearanceController extends Controller
{
    public function edit(): View
    {
        return view('settings.appearance');
    }
}
