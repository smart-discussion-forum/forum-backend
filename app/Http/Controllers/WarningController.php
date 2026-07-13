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

class WarningController extends Controller
{
    /**
     * Manually issue a warning to a user for any reason a Lecturer/Admin
     * types in. This is an EXTRA feature on top of the SDD's automatic
     * inactivity-warning flow (handled by the
     * moderation:check-inactive-users scheduled command instead).
     *
     * Manual warnings are tagged with Source = 'manual' and are counted
     * completely separately from automatic 'auto_inactivity' warnings, so
     * this endpoint can never accidentally interfere with, reset, or
     * double-trigger the automatic compliance-window flow.
     */
    public function issue(Request $request)
    {
        $issuer = Auth::user();

        if (! in_array($issuer->role, [RoleEnum::Lecturer, RoleEnum::Admin], true)) {
            return response()->json([
                'message' => 'Forbidden. Only Lecturers or Admins can issue warnings.',
            ], 403);
        }

        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'reason' => 'required|string|max:255',
        ]);

        $targetUser = User::findOrFail($data['user_id']);

        $warning = Warning::create([
            'User_id' => $targetUser->id,
            'Reason' => $data['reason'],
            'Issued_at' => now(),
            'Source' => Warning::SOURCE_MANUAL,
        ]);

        $targetUser->notify(new WarningIssued($warning));

        $manualWarningLimit = (int) config('moderation.manual_warning_limit');
        $manualWarningCount = Warning::forUser($targetUser->id)->manual()->count();
        $blacklistEntry = null;

        if ($manualWarningCount >= $manualWarningLimit) {
            $blacklistDays = (int) config('moderation.blacklist_duration_days');

            $blacklistEntry = Blacklist::create([
                'User_id' => $targetUser->id,
                'Reason' => "Manually escalated: reached {$manualWarningLimit} lecturer/admin-issued warnings.",
                'Blacklisted_at' => now(),
                'Expires_at' => now()->addDays($blacklistDays),
            ]);

            $targetUser->status = StatusEnum::Blacklisted;
            $targetUser->save();

            $targetUser->notify(new UserBlacklisted($blacklistEntry));
        }

        return response()->json([
            'success' => true,
            'warning' => $warning,
            'manual_warning_count' => $manualWarningCount,
            'blacklisted' => (bool) $blacklistEntry,
        ], 201);
    }

    /**
     * List warnings for a specific user, or the authenticated user's own
     * warnings if no user_id query param is supplied. Students may only
     * view their own warnings; Lecturers/Admins can view anyone's.
     * Optional ?source=manual|auto_inactivity to filter by warning type.
     */
    public function index(Request $request)
    {
        $requester = Auth::user();
        $userId = $request->query('user_id', $requester->id);

        if ($userId != $requester->id
            && ! in_array($requester->role, [RoleEnum::Lecturer, RoleEnum::Admin], true)) {
            return response()->json([
                'message' => 'Forbidden. You may only view your own warnings.',
            ], 403);
        }

        $query = Warning::forUser($userId);

        if ($source = $request->query('source')) {
            $query->where('Source', $source);
        }

        $warnings = $query->orderBy('Issued_at', 'desc')->get();

        return response()->json($warnings);
    }
}
