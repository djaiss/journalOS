<?php

declare(strict_types = 1);

namespace App\Http\Controllers\App\Settings;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class LogController extends Controller
{
    public function index(): View
    {
        $logs = Log::query()
            ->where('user_id', Auth::user()->id)
            ->with('journal')
            ->with('user')
            ->latest()
            ->cursorPaginate(10);

        return view('app.settings.logs.index', [
            'logs' => $logs,
        ]);
    }
}
