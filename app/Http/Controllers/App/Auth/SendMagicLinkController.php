<?php

declare(strict_types = 1);

namespace App\Http\Controllers\App\Auth;

use App\Actions\CreateMagicLink;
use App\Enums\EmailType;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Jobs\SendEmail;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SendMagicLinkController extends Controller
{
    public function create(): View
    {
        return view('app.auth.request-magic-link');
    }

    public function store(Request $request): View
    {
        $request->validate([
            'email' => ['required', 'string', 'email', 'max:255'],
        ]);

        $email = mb_strtolower(TextSanitizer::plainText((string) $request->input('email')));

        try {
            $link = new CreateMagicLink(
                email: $email,
            )->execute();

            SendEmail::dispatch(
                emailType: EmailType::MAGIC_LINK_CREATED,
                user: User::query()->where('email', $email)->firstOrFail(),
                parameters: ['link' => $link],
            )->onQueue('high');
        } catch (ModelNotFoundException) {
        }

        return view('app.auth.magic-link-sent');
    }
}
