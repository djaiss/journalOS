<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Settings\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\LogResource;
use App\Models\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class LogController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $logs = Log::where('user_id', Auth::user()->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return LogResource::collection($logs);
    }

    public function show(int $id): LogResource
    {
        $log = Log::find($id);

        if (!$log || $log->user_id === null) {
            abort(404, 'Log not found.');
        }

        if ($log->user_id !== Auth::user()->id) {
            abort(403, 'Unauthorized action.');
        }

        return new LogResource($log);
    }
}
