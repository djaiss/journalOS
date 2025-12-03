<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\EmailSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class EmailSentController extends Controller
{
    public function index(): View
    {
        $emails = EmailSent::where('user_id', Auth::user()->id)
            ->orderBy('sent_at', 'desc')
            ->cursorPaginate(10);

        return view('settings.emails.index', [
            'emails' => $emails,
        ]);
    }
}
