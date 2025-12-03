<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Actions\CreateMagicLink;
use App\Enums\EmailType;
use App\Jobs\SendEmail;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SendMagicLinkController extends Controller
{
    public function create(): View
    {
        return view('auth.request-magic-link');
    }

    public function store(Request $request): View
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        try {
            $link = new CreateMagicLink(
                email: $request->input('email'),
            )->execute();

            SendEmail::dispatch(
                emailType: EmailType::MAGIC_LINK_CREATED,
                user: User::where('email', $request->input('email'))->firstOrFail(),
                parameters: ['link' => $link],
            )->onQueue('high');
        } catch (ModelNotFoundException) {
        }

        return view('auth.magic-link-sent');
    }
}
