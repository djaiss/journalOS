<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

final class CheckOrganization
{
    /**
     * Check if the user is a member of the organization.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $slug = $request->route()->parameter('slug');
        $id = (int) Str::before($slug, '-');

        try {
            $organization = Organization::findOrFail($id);

            if (! Auth::user()->isPartOfOrganization($organization)) {
                abort(403);
            }

            $request->attributes->add(['organization' => $organization]);

            return $next($request);
        } catch (ModelNotFoundException) {
            abort(404);
        }
    }
}
