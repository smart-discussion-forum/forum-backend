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
use Illuminate\Support\Facades\Artisan;
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

        $query = User::withCount([
                'warnings as manual_warnings_count' => function ($query) {
                    $query->manual();
                },
                'warnings as auto_warnings_count' => function ($query) {
                    $query->autoInactivity();
                },
            ]);

        if ($filter === 'active') {
            $query->where('status', StatusEnum::Active);
        } elseif ($filter === 'blacklisted') {
            $query->where('status', StatusEnum::Blacklisted);
        } elseif ($filter === 'warned') {
            $query->whereHas('warnings');
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->get();

        $payload = [
            'users' => $users,
            'filter' => $filter,
            'search' => $search,
            'moderation' => [
                'first_warning_days' => (int) config('moderation.inactivity_first_warning_days'),
                'second_warning_days' => (int) config('moderation.inactivity_second_warning_days'),
                'blacklist_after_days' => (int) config('moderation.inactivity_blacklist_after_days'),
                'blacklist_duration_days' => (int) config('moderation.blacklist_duration_days'),
            ],
        ];

        return $request->expectsJson() ? response()->json($payload) : view('admin.users', $payload);
    }

    /**
     * Manually trigger the automatic inactivity check so admins can
     * apply warnings / blacklists without waiting for the daily schedule.
     */
    public function runInactivityCheck(Request $request)
    {
        Artisan::call('moderation:check-inactive-users');

        $message = trim(Artisan::output()) ?: 'Inactivity check completed.';
        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('status', $message);
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

            $message = "Warning issued and {$user->name} was auto-blacklisted (reached {$manualWarningLimit} warnings).";
            return $request->expectsJson()
                ? response()->json(['message' => $message])
                : back()->with('status', $message);
        }

        $message = "Warning issued to {$user->name}.";
        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('status', $message);
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

        $message = "{$user->name} has been blacklisted.";
        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('status', $message);
    }

    /**
     * Lift an active blacklist and reinstate the user, matching
     * BlacklistController@lift.
     */
    public function reinstate(Request $request, User $user)
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

        $message = "{$user->name} has been reinstated.";
        return $request->expectsJson()
            ? response()->json(['message' => $message])
            : back()->with('status', $message);
    }
}
