<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use App\Models\Blacklist;
use App\Models\User;
use App\Models\Warning;
use App\Notifications\UserBlacklisted;
use App\Notifications\WarningIssued;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminUserController extends Controller
{
    /**
     * List all users with their warning count and current status, so an
     * Admin can issue warnings or blacklist/reinstate someone directly
     * from the dashboard. Reuses the same Warning/Blacklist logic as the
     * JSON endpoints in WarningController and BlacklistController.
     */
    public function index()
    {
        $users = User::withCount(['warnings' => function ($query) {
                $query->manual();
            }])
            ->orderBy('name')
            ->get();

        return view('admin.users', compact('users'));
    }

    /**
     * Issue a manual warning to a user from the admin dashboard.
     * Mirrors WarningController@issue, including the auto-blacklist
     * escalation once the manual warning limit is reached.
     */
    public function warn(Request $request, User $user)
    {
        $data = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        $warning = Warning::create([
            'User_id' => $user->id,
            'Reason' => $data['reason'],
            'Issued_at' => now(),
            'Source' => Warning::SOURCE_MANUAL,
        ]);

        $user->notify(new WarningIssued($warning));

        $manualWarningLimit = (int) config('moderation.manual_warning_limit');
        $manualWarningCount = Warning::forUser($user->id)->manual()->count();

        if ($manualWarningCount >= $manualWarningLimit && $user->status !== StatusEnum::Blacklisted) {
            $blacklistDays = (int) config('moderation.blacklist_duration_days');

            $blacklistEntry = Blacklist::create([
                'User_id' => $user->id,
                'Reason' => "Manually escalated: reached {$manualWarningLimit} lecturer/admin-issued warnings.",
                'Blacklisted_at' => now(),
                'Expires_at' => now()->addDays($blacklistDays),
            ]);

            $user->status = StatusEnum::Blacklisted;
            $user->save();

            $user->notify(new UserBlacklisted($blacklistEntry));

            return back()->with('status', "Warning issued and {$user->name} was auto-blacklisted (reached {$manualWarningLimit} warnings).");
        }

        return back()->with('status', "Warning issued to {$user->name}.");
    }

    /**
     * Blacklist a user directly (Admin override, independent of the
     * warning-count threshold).
     */
    public function blacklist(Request $request, User $user)
    {
        $data = $request->validate([
            'reason' => 'nullable|string|max:255',
        ]);

        $blacklistDays = (int) config('moderation.blacklist_duration_days');

        $entry = Blacklist::create([
            'User_id' => $user->id,
            'Reason' => $data['reason'] ?: 'Blacklisted by Admin.',
            'Blacklisted_at' => now(),
            'Expires_at' => now()->addDays($blacklistDays),
        ]);

        $user->status = StatusEnum::Blacklisted;
        $user->save();

        $user->notify(new UserBlacklisted($entry));

        return back()->with('status', "{$user->name} has been blacklisted.");
    }

    /**
     * Lift an active blacklist and reinstate the user, matching
     * BlacklistController@lift.
     */
    public function reinstate(User $user)
    {
        Blacklist::where('User_id', $user->id)
            ->get()
            ->each(function (Blacklist $entry) {
                $entry->Expires_at = now();
                $entry->save();
            });

        $user->status = StatusEnum::Active;
        $user->save();

        return back()->with('status', "{$user->name} has been reinstated.");
    }
}
