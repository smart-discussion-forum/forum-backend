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
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $search = trim((string) $request->query('search', ''));

        $query = User::where('role', '!=', RoleEnum::Admin)
            ->withCount(['warnings' => function ($query) {
                $query->manual();
            }])
            ->orderBy('name');

        match ($filter) {
            'active' => $query->where('status', StatusEnum::Active),
            'blacklisted' => $query->where('status', StatusEnum::Blacklisted),
            'warned' => $query->whereHas('warnings', function ($query) {
                $query->manual();
            }),
            default => null,
        };

        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get();

        return view('admin.users', compact('users', 'filter', 'search'));
    }

    /**
     * Full detail page for a single user: their group involvement,
     * activity stats, and full warning/blacklist history, plus the
     * action forms to warn, blacklist, or reinstate them.
     */
    public function show(User $user)
    {
        abort_if($user->role === RoleEnum::Admin, 404);

        $user->loadCount([
            'createdGroups',
            'groups',
            'topics',
            'posts',
            'sentMessages',
            'warnings' => fn ($query) => $query->manual(),
        ]);

        $createdGroups = $user->createdGroups()->withCount(['members', 'topics'])->get();
        $joinedGroups = $user->groups()->withCount(['members', 'topics'])->get();

        $warnings = $user->warnings()->orderByDesc('Issued_at')->get();
        $blacklistEntries = $user->blacklistEntries()->orderByDesc('Blacklisted_at')->get();
        $activeBlacklistEntry = $blacklistEntries->first(fn (Blacklist $entry) => $entry->isActive());

        return view('admin.users-show', compact(
            'user', 'createdGroups', 'joinedGroups', 'warnings', 'blacklistEntries', 'activeBlacklistEntry'
        ));
    }

    /**
     * Issue a manual warning to a user from the admin dashboard.
     * Mirrors WarningController@issue, including the auto-blacklist
     * escalation once the manual warning limit is reached.
     */
    public function warn(Request $request, User $user)
    {
        abort_if($user->role === RoleEnum::Admin, 404);

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
        abort_if($user->role === RoleEnum::Admin, 404);

        $data = $request->validate([
            'reason' => 'nullable|string|max:255',
            'duration_days' => 'nullable|integer|min:1|max:365',
        ]);

        $blacklistDays = $data['duration_days'] ?? (int) config('moderation.blacklist_duration_days');

        $entry = Blacklist::create([
            'User_id' => $user->id,
            'Reason' => $data['reason'] ?? 'Blacklisted by Admin.',
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
        abort_if($user->role === RoleEnum::Admin, 404);
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
