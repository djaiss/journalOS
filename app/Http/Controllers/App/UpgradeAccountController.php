<?php

declare(strict_types = 1);

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class UpgradeAccountController extends Controller
{
    public function index(): View
    {
        return view('app.upgrade.index', [
            'user' => Auth::user(),
        ]);
    }
}
