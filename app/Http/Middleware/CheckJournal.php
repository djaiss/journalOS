<?php

declare(strict_types = 1);

namespace App\Http\Middleware;

use App\Models\Journal;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class CheckJournal
{
    /**
     * Check if the user has access to the journal.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route()->parameter('slug');
        $id = (int) Str::before($slug, '-');

        try {
            $journal = Journal::query()
                ->where('id', $id)
                ->where('user_id', Auth::user()->id)
                ->firstOrFail();
            $request->attributes->add(['journal' => $journal]);

            return $next($request);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }
}
