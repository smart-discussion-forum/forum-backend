<?php
namespace App\Http\Controllers;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class GroupController extends Controller
{
public function create()
{
    return view('groups.create');
}
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

    if ($request->wantsJson()) {
        return response()->json($group->load('members'), 201);
    }

    return redirect()->route('groups.index')->with('success', 'Group created successfully.');
}

    public function index()
    {
        $user = Auth::user();

        $myGroups = $user->groups()->get();
        $myGroupIds = $myGroups->pluck('id');

        $joinableGroups = Group::whereNotIn('id', $myGroupIds)
            ->withCount('members')
            ->get();

        return view('groups.index', compact('myGroups', 'joinableGroups'));
    }

    public function show($id)
    {
        $group = Group::with(['members', 'topics', 'creator'])->findOrFail($id);
        return response()->json($group);
    }

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

        return redirect()->route('groups.index')->with('success', 'Joined group successfully.');
    }

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

        return redirect()->route('groups.index')->with('success', 'Left group successfully.');
    }

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

    public function manage()
    {
        $groups = Group::with('creator')
            ->withCount(['members', 'topics'])
            ->latest()
            ->get();

        return view('groups.manage', compact('groups'));
    }

    public function edit($id)
    {
        $group = Group::findOrFail($id);
        return view('groups.edit', compact('group'));
    }

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

    public function destroy($id)
    {
        $group = Group::findOrFail($id);
        $group->delete();

        return redirect()->route('groups.manage')->with('success', 'Group deleted successfully.');
    }
}
