<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsStudent
{
    /**
     * Allow the request through only if the authenticated user is a Student.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== RoleEnum::Student) {
            abort(403, 'This action is restricted to Students.');
        }

        return $next($request);
    }
}
