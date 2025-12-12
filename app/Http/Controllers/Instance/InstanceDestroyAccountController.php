<?php

declare(strict_types=1);

namespace App\Http\Controllers\Instance;

use App\Http\Controllers\Controller;
use App\Actions\DestroyAccountAsInstanceAdministrator;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class InstanceDestroyAccountController extends Controller
{
    public function destroy(Request $request, User $user): RedirectResponse
    {
        new DestroyAccountAsInstanceAdministrator(
            user: Auth::user(),
            account: $user,
        )->execute();

        return to_route('instance.index')
            ->with('status', trans('The account has been deleted'));
    }
}
