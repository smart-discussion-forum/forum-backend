<?php

namespace App\Http\Middleware;

use App\Enums\RoleEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsLecturer
{
    /**
     * Allow the request through only if the authenticated user is a Lecturer
     * or an Admin (Admins are treated as a superset of Lecturer permissions).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = $request->user()?->role;

        if (! in_array($role, [RoleEnum::Lecturer, RoleEnum::Admin], true)) {
            abort(403, 'This action is restricted to lecturers or administrators.');
        }

        return $next($request);
    }
}
