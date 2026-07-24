<?php

namespace App\Providers;

use App\Enums\StatusEnum;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Broadcast::routes(['middleware' => ['auth:sanctum']]);

        View::composer('layouts.app', function ($view) {
            $user = auth()->user();

            if (! $user) {
                return;
            }

            $view->with('navUnreadNotificationsCount', $user->unreadNotifications()->count());
            $view->with('navRecentNotifications', $user->notifications()->latest()->take(6)->get());

            $activeBlacklistEntry = null;
            if ($user->status === StatusEnum::Blacklisted) {
                $activeBlacklistEntry = $user->blacklistEntries()
                    ->orderByDesc('Blacklisted_at')
                    ->get()
                    ->first(fn ($entry) => $entry->isActive());
            }
            $view->with('activeBlacklistEntry', $activeBlacklistEntry);
        });
    }
}