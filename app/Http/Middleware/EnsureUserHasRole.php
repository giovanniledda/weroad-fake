<?php

namespace App\Http\Middleware;

use function abort;
use Closure;
use Illuminate\Http\Request;

class EnsureUserHasRole
{
    /**
     * @param Request $request
     * @param Closure $next
     * @param string $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role): mixed
    {
        if (! $request->user()->hasRole($role)) {
            abort(401);
        }

        return $next($request);
    }
}