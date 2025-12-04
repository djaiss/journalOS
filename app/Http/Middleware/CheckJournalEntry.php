<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Actions\CreateOrRetrieveJournalEntry;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class CheckJournalEntry
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $day = (int) $request->route()->parameter('day');
        $month = (int) $request->route()->parameter('month');
        $year = (int) $request->route()->parameter('year');

        try {
            $journal = $request->attributes->get('journal');

            $entry = new CreateOrRetrieveJournalEntry(
                user: Auth::user(),
                journal: $journal,
                day: $day,
                month: $month,
                year: $year,
            )->execute();
        } catch (ModelNotFoundException) {
            abort(404);
        }

        $request->attributes->add(['journal_entry' => $entry]);

        return $next($request);
    }
}
