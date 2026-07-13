<?php

namespace App\Http\Middleware;

use App\Enums\StatusEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureNotBlacklisted
{
    /**
     * Block requests from users whose status is Blacklisted.
     * Register this as the 'not_blacklisted' alias in bootstrap/app.php,
     * then apply it to routes that create posts, messages, direct
     * messages, or topics.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->status === StatusEnum::Blacklisted) {
            return response()->json([
                'message' => 'Your account has been blacklisted and can no longer post or send messages.',
            ], 403);
        }

        return $next($request);
    }
}
