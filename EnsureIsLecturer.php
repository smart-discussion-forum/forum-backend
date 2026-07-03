<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsLecturer
{
    /**
     * Allow the request through only if the authenticated user is a Lecturer.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->user()->role !== RoleEnum::Lecturer) {
            abort(403, 'This action is restricted to Lecturers.');
        }

        return $next($request);
    }
}
