<?php

namespace App\Http\Middleware;

use function abort;
use Closure;
use Illuminate\Http\Request;
use function response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        if (! $request->user()->hasRole($role)) {
//            abort(401);
            return response()
                ->json(['message' => 'Unauthorized: action denied!'], 401);
        }

        return $next($request);
    }
}
