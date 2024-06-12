<?php

namespace App\Http\Middleware;

use App\Models\Journal;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckJournal
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (is_string($request->route()->parameter('journal'))) {
            $id = (int) $request->route()->parameter('journal');
        } else {
            $id = $request->route()->parameter('journal')->id;
        }

        try {
            $journal = Journal::where('user_id', auth()->user()->id)->findOrFail($id);

            // this makes the journal available in the request
            // like $request->attributes->get('journal'), in your controllers
            $request->attributes->add(['journal' => $journal]);

            return $next($request);
        } catch (ModelNotFoundException) {
            abort(401);
        }
    }
}
