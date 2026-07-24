<?php
namespace App\Http\Controllers;
use App\Models\Group;
use App\Models\ParticipationMark;
use App\Models\User;
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

    public function statistics(Request $request, $id)
    {
        $group = Group::with(['creator'])->findOrFail($id);
        $currentUser = Auth::user();

        if (! $currentUser) {
            abort(403);
        }

        $role = $currentUser->role?->value;
        if (! in_array($role, ['Lecturer', 'Admin'], true)) {
            abort(403, 'Participation marks are only available to lecturers.');
        }

        $allowedGroupIds = collect();
        if ($role === 'Admin') {
            $allowedGroupIds = Group::pluck('id');
        } else {
            $allowedGroupIds = $currentUser->createdGroups()->pluck('groups.id')
                ->merge($currentUser->groups()->pluck('groups.id'));
        }

        if (! $allowedGroupIds->contains($group->id)) {
            abort(403);
        }

        $studentCount = $group->members()
            ->where('users.role', 'student')
            ->count();

        $topicCount = $group->topics()->count();
        $messageCount = $group->messages()->count();
        $postCount = $group->topics()
            ->withCount('posts')
            ->get()
            ->sum('posts_count');

        $sortBy = $request->query('sort_by', 'participation_score');
        $sortOrder = $request->query('sort_order', 'desc');

        $participationRows = $group->members()
            ->select('users.id', 'users.name', 'users.email', 'users.last_active', 'users.role')
            ->where('users.role', 'student')
            ->withCount(['posts' => function ($query) use ($group) {
                $query->whereHas('topic', function ($topicQuery) use ($group) {
                    $topicQuery->where('group_id', $group->id);
                });
            }])
            ->get()
            ->map(function ($member) use ($group) {
                $memberPostCount = (int) $member->posts_count;
                $mark = ParticipationMark::awardForUserInGroup((int) $member->id, (int) $group->id);

                return [
                    'user_id' => $member->id,
                    'name' => $member->name,
                    'email' => $member->email,
                    'post_count' => $memberPostCount,
                    'participation_score' => round((float) ($mark?->score ?? 0), 2),
                    'activity_status' => $this->activityStatus($member),
                ];
            })
            ->values();

        $sortMap = [
            'name' => 'name',
            'post_count' => 'post_count',
            'participation_score' => 'participation_score',
            'activity_status' => 'activity_status',
        ];
        $sortKey = $sortMap[$sortBy] ?? 'participation_score';

        if ($sortOrder === 'asc') {
            $participationRows = $participationRows->sortBy($sortKey)->values();
        } else {
            $participationRows = $participationRows->sortByDesc($sortKey)->values();
        }

        $averageScore = $participationRows->avg('participation_score') ?? 0;
        $topScore = $participationRows->max('participation_score') ?? 0;

        return view('groups.statistics', [
            'group_name' => $group->name,
            'created_by' => $group->creator?->name,
            'student_count' => $studentCount,
            'topic_count' => $topicCount,
            'post_count' => $postCount,
            'message_count' => $messageCount,
            'average_score' => round((float) $averageScore, 1),
            'top_score' => round((float) $topScore, 1),
            'selected_group_id' => $group->id,
            'participation_rows' => $participationRows,
            'sort_by' => $sortBy,
            'sort_order' => $sortOrder,
        ]);
    }

    private function activityStatus(User $user): string
    {
        if (! $user->last_active) {
            return 'Inactive';
        }

        return $user->last_active->gt(now()->subDays(7)) ? 'Active' : 'Inactive';
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

