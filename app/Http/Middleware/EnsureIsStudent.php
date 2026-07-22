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
     * Registered as the 'is_student' alias in bootstrap/app.php.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user || $user->role !== RoleEnum::Student) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Forbidden. This action requires the Student role.',
                ], 403);
            }
            abort(403, 'This page is only available to Students.');
        }
        return $next($request);
    }
}
