<?php

namespace App\Http\Middleware;

use App\Enums\StatusEnum;
use App\Models\Blacklist;
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
            $stillActive = Blacklist::query()
                ->where('User_id', $user->id)
                ->where(function ($query) {
                    $query->whereNull('Expires_at')
                        ->orWhere('Expires_at', '>', now());
                })
                ->exists();

            // Auto-reinstate if every blacklist entry has expired.
            if (! $stillActive) {
                $user->status = StatusEnum::Active;
                $user->save();

                return $next($request);
            }

            return response()->json([
                'message' => 'Your account has been blacklisted and can no longer post or send messages.',
            ], 403);
        }

        return $next($request);
    }
}
