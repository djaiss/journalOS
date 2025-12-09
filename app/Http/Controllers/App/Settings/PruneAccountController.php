<?php

declare(strict_types=1);

namespace App\Http\Controllers\App\Settings;

use App\Actions\PruneAccount;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

final class PruneAccountController extends Controller
{
    public function update(): RedirectResponse
    {
        new PruneAccount(
            user: Auth::user(),
        )->execute();

        return to_route('settings.account.index')
            ->with('status', trans('The account has been pruned'));
    }
}
