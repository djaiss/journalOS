<?php

declare(strict_types = 1);

namespace App\Http\Controllers\App;

use App\Actions\CreateGuestAccount;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class DemoAccountController extends Controller
{
    public function index(): RedirectResponse
    {
        if (Auth::check()) {
            return to_route('journal.index');
        }

        $user = ( new CreateGuestAccount )->execute();

        Auth::login($user, true);

        return to_route('journal.index');
    }
}
