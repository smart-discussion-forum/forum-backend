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

    /**
     * GET /groups/{id}/statistics
     * Returns a styled HTML page with membership and activity stats
     * for a single group. Restricted to Admin via the 'admin' middleware.
     */
    public function statistics($id)
    {
        $group = Group::with(['creator'])->findOrFail($id);

        $memberCount = $group->members()->count();
        $topicCount = $group->topics()->count();
        $messageCount = $group->messages()->count();

        $postCount = $group->topics()
            ->withCount('posts')
            ->get()
            ->sum('posts_count');

        $membersByRole = $group->members()
            ->get()
            ->groupBy('pivot.role')
            ->map(fn ($members) => $members->count());

        return view('groups.statistics', [
            'group_name' => $group->name,
            'created_by' => $group->creator?->name,
            'member_count' => $memberCount,
            'topic_count' => $topicCount,
            'post_count' => $postCount,
            'message_count' => $messageCount,
            'members_by_role' => $membersByRole,
        ]);
    }

    /**
     * GET /admin/groups
     * Admin-only page listing every group in the system, with links to
     * edit or delete each one.
     */
    public function manage()
    {
        $groups = Group::with('creator')
            ->withCount(['members', 'topics'])
            ->latest()
            ->get();

        return view('groups.manage', compact('groups'));
    }

    /**
     * GET /groups/{id}/edit
     * Admin-only edit form for a group's name/description.
     */
    public function edit($id)
    {
        $group = Group::findOrFail($id);
        return view('groups.edit', compact('group'));
    }

    /**
     * PUT /groups/{id}
     * Admin-only update of a group's name/description.
     */
    public function update(Request $request, $id)
    {
        $group = Group::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);

        $group->update($validated);

        return redirect()->route('groups.manage')->with('success', 'Group updated successfully.');
    }

    /**
     * DELETE /groups/{id}
     * Admin-only deletion of a group.
     */
    public function destroy($id)
    {
        $group = Group::findOrFail($id);
        $group->delete();

        return redirect()->route('groups.manage')->with('success', 'Group deleted successfully.');
    }
}
