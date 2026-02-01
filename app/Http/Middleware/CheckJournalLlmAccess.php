<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Models\Journal;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class CheckJournalLlmAccess
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $accessKey = (string) $request->route()->parameter('accessKey');

        try {
            $journal = Journal::query()
                ->where('has_llm_access', true)
                ->where('llm_access_key', $accessKey)
                ->firstOrFail();

            $request->attributes->add(['journal' => $journal]);

            return $next($request);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }
}
