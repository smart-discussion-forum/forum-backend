<?php

namespace App\Http\Controllers;

use App\Enums\RoleEnum;
use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    /**
     * List all groups the authenticated user belongs to.
     */
    public function index(Request $request)
    {
        $groups = $request->user()->groups()->latest()->get();

        return response()->json(['groups' => $groups]);
    }

    /**
     * Create a new group. Restricted to Lecturer or Admin.
     *
     * Group creation needs to allow EITHER of two roles, so it's checked
     * here rather than via a single-role middleware (EnsureIsAdmin /
     * EnsureIsLecturer each only allow one role and can't be OR'd by
     * stacking them on a route).
     */
    public function store(Request $request)
    {
        $user = $request->user();

        if (! in_array($user->role, [RoleEnum::Lecturer, RoleEnum::Admin], true)) {
            abort(403, 'Only Lecturers or Admins can create groups.');
        }

        $data = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $group = Group::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'created_by' => $user->id,
        ]);

        // Creator is automatically a member of their own group.
        $group->members()->attach($user->id, [
            'role' => 'Moderator',
            'joined_at' => now(),
        ]);

        return response()->json(['group' => $group], 201);
    }

    /**
     * View a single group with its members and topics.
     */
    public function show(Request $request, Group $group)
    {
        $group->load(['members', 'topics']);

        return response()->json(['group' => $group]);
    }

    /**
     * Join a group. Restricted to the Student role.
     * Prevents duplicate joins / joining a group already belonged to.
     */
    public function join(Request $request, Group $group)
    {
        $user = $request->user();

        if ($group->hasMember($user)) {
            return response()->json([
                'message' => 'You are already a member of this group.',
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
     * Leave a group.
     */
    public function leave(Request $request, Group $group)
    {
        $user = $request->user();

        if (! $group->hasMember($user)) {
            return response()->json([
                'message' => 'You are not a member of this group.',
            ], 409);
        }

        $group->members()->detach($user->id);

        return response()->json(['message' => 'Left group successfully.']);
    }
}
