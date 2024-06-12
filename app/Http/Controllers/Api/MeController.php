<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * This displays the information about the logged user.
 */
class MeController extends Controller
{
    /**
     * Get the information about the logged user
     *
     * This endpoint gets the information about the logged user.
     *
     * @response 200 {
     *  "id": 4,
     *  "name": "Jessica Jones",
     *  "email": "jessica.jones@gmail.com"
     * }
     */
    public function show(Request $request): JsonResponse
    {
        $response = [
            'id' => $request->user()->id,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
        ];

        return response()->json($response);
    }
}
