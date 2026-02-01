<?php

declare(strict_types = 1);

namespace App\Http\Controllers\App\Journals\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class JournalModulesSettingsController extends Controller
{
    public function show(Request $request): View
    {
        $journal = $request->attributes->get('journal');
        $layouts = $journal
            ->layouts()
            ->orderByDesc('created_at')
            ->get();

        return view('app.journal.settings.modules.index', [
            'journal' => $journal,
            'layouts' => $layouts,
        ]);
    }
}
