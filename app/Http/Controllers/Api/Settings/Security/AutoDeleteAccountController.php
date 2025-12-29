<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Settings\Security;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class AutoDeleteAccountController extends Controller
{
    use ApiResponses;

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'auto_delete_account' => ['required', 'boolean'],
        ]);

        $user = Auth::user();
        $user->auto_delete_account = (bool) $validated['auto_delete_account'];
        $user->save();

        return $this->ok(trans('Changes saved'));
    }
}
