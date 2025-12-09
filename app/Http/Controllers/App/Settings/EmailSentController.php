<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Http\Controllers\Controller;
use App\Models\EmailSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class EmailSentController extends Controller
{
    public function index(): View
    {
        $emails = EmailSent::query()->where('user_id', Auth::user()->id)
            ->latest('sent_at')
            ->cursorPaginate(10);

        return view('app.settings.emails.index', [
            'emails' => $emails,
        ]);
    }
}
