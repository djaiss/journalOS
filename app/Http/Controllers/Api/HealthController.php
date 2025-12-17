<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;

final class HealthController extends Controller
{
    use ApiResponses;

    public function show(): JsonResponse
    {
        return $this->ok('ok');
    }
}
