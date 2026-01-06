<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Settings\Account;

use App\Actions\DestroyAccount;
use App\Helpers\TextSanitizer;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

final class DestroyAccountController extends Controller
{
    use ApiResponses;

    public function destroy(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string', 'min:3', 'max:255'],
        ]);

        new DestroyAccount(
            user: Auth::user(),
            reason: TextSanitizer::plainText($validated['reason']),
        )->execute();

        return $this->ok(trans('The account has been deleted'));
    }
}
