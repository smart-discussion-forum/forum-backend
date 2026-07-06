<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    /**
     * Allow the request through only if the authenticated user is an Admin.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== RoleEnum::Admin) {
            abort(403, 'This action is restricted to administrators.');
        }

        return $next($request);
    }
}
