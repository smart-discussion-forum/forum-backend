<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Enums\StatusEnum;
use App\Models\Blacklist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlacklistController extends Controller
{
    /**
     * Show the authenticated user's own blacklist status/history.
     * Anyone can view this for themselves, regardless of role.
     */
    public function status()
    {
        $user = Auth::user();

        $entries = Blacklist::where('User_id', $user->id)
            ->orderByDesc('Blacklisted_at')
            ->get();

        $activeEntry = $entries->first(fn (Blacklist $entry) => $entry->isActive());

        return view('blacklist.status', [
            'entries' => $entries,
            'activeEntry' => $activeEntry,
        ]);
    }

    /**
     * List currently-active blacklist entries. Lecturers/Admins only.
     */
    public function index(Request $request)
    {
        $requester = Auth::user();

        if (! in_array($requester->role, [RoleEnum::Lecturer, RoleEnum::Admin], true)) {
            return response()->json([
                'message' => 'Forbidden. Only Lecturers or Admins can view the blacklist.',
            ], 403);
        }

        $entries = Blacklist::with('user:id,name,email')
            ->orderBy('Blacklisted_at', 'desc')
            ->get()
            ->filter(fn (Blacklist $entry) => $entry->isActive())
            ->values();

        return response()->json($entries);
    }

    /**
     * Manually lift a blacklist entry and reinstate the user. Admins only.
     */
    public function lift(Request $request, $blacklistId)
    {
        $requester = Auth::user();

        if ($requester->role !== RoleEnum::Admin) {
            return response()->json([
                'message' => 'Forbidden. Only Admins can lift a blacklist.',
            ], 403);
        }

        $entry = Blacklist::findOrFail($blacklistId);
        $entry->Expires_at = now();
        $entry->save();

        $user = User::find($entry->User_id);
        if ($user) {
            $user->status = StatusEnum::Active;
            $user->save();
        }

        return response()->json(['success' => true]);
    }
}
