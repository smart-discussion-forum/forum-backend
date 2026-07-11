<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureIsAdmin
{
    /**
     * Allow the request through only if the authenticated user is an Admin.
     * Registered as the 'is_admin' alias in bootstrap/app.php.
     *
     * Works for both Blade (redirects/aborts with a 403 page) and JSON API
     * requests (returns a 403 JSON payload), based on what the client expects.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== User::ROLE_ADMIN) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Forbidden. This action requires the Admin role.',
                ], 403);
            }

            abort(403, 'This page is only available to Admins.');
        }

        return $next($request);
    }
}
