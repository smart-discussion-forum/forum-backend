<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * POST /groups
     * Create a new group. Restricted to Lecturer/Admin via 'lecturer' middleware.
     * The creator is automatically added as a Moderator member.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);

        $group = Group::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'created_by' => Auth::id(),
        ]);

        $group->members()->attach(Auth::id(), [
            'role' => 'Moderator',
            'joined_at' => now(),
        ]);

        return response()->json($group->load('members'), 201);
    }

    /**
     * GET /groups
     * List all groups the authenticated user belongs to.
     */
    public function index()
    {
        $groups = Auth::user()->groups()->get();

        return response()->json($groups);
    }

    /**
     * GET /groups/{id}
     * View a single group with its members and topics.
     */
    public function show($id)
    {
        $group = Group::with(['members', 'topics', 'creator'])->findOrFail($id);

        return response()->json($group);
    }

    /**
     * POST /groups/{id}/join
     * Join a group. Restricted to Student via 'student' middleware.
     * Prevents joining a group the user already belongs to.
     */
    public function join($id)
    {
        $group = Group::findOrFail($id);
        $user = Auth::user();

        if ($group->members()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'You already belong to this group.',
            ], 409);
        }

        $group->members()->attach($user->id, [
            'role' => 'Member',
            'joined_at' => now(),
        ]);

        return response()->json([
            'message' => 'Joined group successfully.',
            'group' => $group->load('members'),
        ]);
    }

    /**
     * POST /groups/{id}/leave
     * Leave a group. Any authenticated member may leave.
     */
    public function leave($id)
    {
        $group = Group::findOrFail($id);
        $user = Auth::user();

        if (! $group->members()->where('user_id', $user->id)->exists()) {
            return response()->json([
                'message' => 'You are not a member of this group.',
            ], 409);
        }

        $group->members()->detach($user->id);

        return response()->json([
            'message' => 'Left group successfully.',
        ]);
    }
}
