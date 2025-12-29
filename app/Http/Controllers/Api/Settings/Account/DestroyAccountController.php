<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Settings\Account;

use App\Actions\DestroyAccount;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Settings\Account\DestroyAccountRequest;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class DestroyAccountController extends Controller
{
    use ApiResponses;

    public function destroy(DestroyAccountRequest $request): JsonResponse
    {
        $validated = $request->validated();

        new DestroyAccount(
            user: Auth::user(),
            reason: $validated['reason'],
        )->execute();

        return $this->ok(trans('The account has been deleted'));
    }
}
