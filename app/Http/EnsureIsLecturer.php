<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsLecturer
{
    /**
     * Allow the request through if the user is a Lecturer, OR an Admin
     * (Admins are a superset of Lecturer permissions). Registered as the
     * 'is_lecturer' alias in bootstrap/app.php.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, [User::ROLE_LECTURER, User::ROLE_ADMIN], true)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Forbidden. This action requires the Lecturer role.',
                ], 403);
            }

            abort(403, 'This page is only available to Lecturers.');
        }

        return $next($request);
    }
}
