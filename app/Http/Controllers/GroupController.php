<?php
namespace App\Http\Controllers;
use App\Models\Group;
use App\Models\ParticipationMark;
use App\Models\Post;
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

        $allowedGroupIds = collect();
        if ($currentUser->role?->value === 'Admin') {
            $allowedGroupIds = Group::pluck('id');
        } elseif ($currentUser->role?->value === 'Lecturer') {
            $allowedGroupIds = $currentUser->createdGroups()->pluck('groups.id')
                ->merge($currentUser->groups()->pluck('groups.id'));
        } else {
            $allowedGroupIds = $currentUser->groups()->pluck('groups.id');
        }

        if (! $allowedGroupIds->contains($group->id)) {
            abort(403);
        }

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

        $selectedGroupId = $request->query('group_id', $group->id);
        $selectedGroup = $selectedGroupId ? Group::find($selectedGroupId) : null;

        $visibleGroups = Group::query()
            ->when($currentUser->role?->value === 'Lecturer', function ($query) use ($currentUser) {
                $query->whereIn('id', $currentUser->createdGroups()->pluck('groups.id')->merge($currentUser->groups()->pluck('groups.id')));
            })
            ->when($currentUser->role?->value === 'student', function ($query) use ($currentUser) {
                $query->whereIn('id', $currentUser->groups()->pluck('groups.id'));
            })
            ->orderBy('name')
            ->get();

        $participationRows = collect();
        if ($selectedGroup) {
            $participationRows = $selectedGroup->members()
                ->select('users.id', 'users.name', 'users.email', 'users.last_active')
                ->withCount(['posts' => function ($query) use ($selectedGroup) {
                    $query->whereHas('topic', function ($topicQuery) use ($selectedGroup) {
                        $topicQuery->where('group_id', $selectedGroup->id);
                    });
                }])
                ->get()
                ->map(function ($member) use ($selectedGroup) {
                    $postCount = (int) $member->posts_count;
                    
                    $dbScore = ParticipationMark::where('user_id', $member->id)
                        ->where('group_id', $selectedGroup->id)
                        ->value('score');
                    
                    $score = $dbScore ?? (($postCount * 2.5) + (rand(0, 30)));

                    return [
                        'user_id' => $member->id,
                        'name' => $member->name,
                        'email' => $member->email,
                        'post_count' => $postCount,
                        'participation_score' => round((float) $score, 2),
                        'activity_status' => $this->activityStatus($member),
                    ];
                })
                ->values();

            $sortBy = $request->query('sort_by', 'participation_score');
            $sortOrder = $request->query('sort_order', 'desc');

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
            
            if ($currentUser->role?->value === 'student') {
                $participationRows = $participationRows->filter(fn ($row) => $row['user_id'] === $currentUser->id)->values();
            } elseif ($currentUser->role?->value === 'Lecturer') {
                $topicIds = $selectedGroup->topics()->pluck('id');
                if ($topicIds->isNotEmpty()) {
                    $studentIds = Post::whereIn('topic_id', $topicIds)
                        ->pluck('user_id')
                        ->unique()
                        ->merge($selectedGroup->members()->pluck('users.id'))
                        ->unique();
                    $participationRows = $participationRows->filter(fn ($row) => $studentIds->contains($row['user_id']))->values();
                }
            }
        }

        return view('groups.statistics', [
            'group_name' => $group->name,
            'created_by' => $group->creator?->name,
            'member_count' => $memberCount,
            'topic_count' => $topicCount,
            'post_count' => $postCount,
            'message_count' => $messageCount,
            'members_by_role' => $membersByRole,
            'visible_groups' => $visibleGroups,
            'selected_group_id' => $selectedGroup?->id ?? $group->id,
            'participation_rows' => $participationRows,
            'can_view_all' => $currentUser->role?->value === 'Admin',
            'current_user' => $currentUser,
            'sort_by' => $request->query('sort_by', 'participation_score'),
            'sort_order' => $request->query('sort_order', 'desc'),
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
