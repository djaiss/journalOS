<?php

declare(strict_types = 1);

namespace App\Http\Controllers\Api\Settings\Account;

use App\Actions\PruneAccount;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class PruneAccountController extends Controller
{
    use ApiResponses;

    public function update(): JsonResponse
    {
        new PruneAccount(
            user: Auth::user(),
        )->execute();

        return $this->ok(trans('The account has been pruned'));
    }
}
