<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class LogController extends Controller
{
    public function index(): View
    {
        $logs = Log::where('user_id', Auth::user()->id)
            ->with('organization')
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->cursorPaginate(10);

        return view('settings.logs.index', [
            'logs' => $logs,
        ]);
    }
}
