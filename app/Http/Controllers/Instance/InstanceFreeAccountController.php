<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instance;

use App\Http\Controllers\Controller;
use App\Actions\GiveFreeAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class InstanceFreeAccountController extends Controller
{
    public function update(Request $request, User $user): RedirectResponse
    {
        new GiveFreeAccount(
            user: Auth::user(),
            account: $user,
        )->execute();

        return to_route('instance.show', [
            'user' => $user,
        ])->with('status', trans('The account is now free'));
    }
}
