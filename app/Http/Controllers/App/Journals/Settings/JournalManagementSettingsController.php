<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Journals\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class JournalManagementSettingsController extends Controller
{
    public function show(Request $request): View
    {
        $journal = $request->attributes->get('journal');

        return view('app.journal.settings.management.index', [
            'journal' => $journal,
        ]);
    }
}
