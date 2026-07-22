<?php

namespace App\Http\Controllers;

use App\Models\Group;
use Illuminate\Http\Request;

class GroupController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $myGroups = $user->groups()
            ->withPivot('role')
            ->orderBy('groups.name')
            ->get();

        $joinableGroups = Group::query()
            ->whereDoesntHave('members', function ($query) use ($user) {
                $query->where('users.id', $user->id);
            })
            ->withCount('members')
            ->orderBy('name')
            ->get();

        return view('groups.index', compact('myGroups', 'joinableGroups'));
    }

    public function show(Group $group)
    {
        $user = auth()->user();

        if (! $user->groups()->where('groups.id', $group->id)->exists()) {
            abort(403);
        }

        return redirect('/groups/' . $group->id . '/topics');
    }

    public function join(Group $group)
    {
        $user = auth()->user();

        if ($user->groups()->where('groups.id', $group->id)->exists()) {
            return back()->with('success', 'You are already a member of this group.');
        }

        $user->groups()->attach($group->id, ['role' => 'Member', 'joined_at' => now()]);

        return back()->with('success', 'You joined the group.');
    }

    public function leave(Group $group)
    {
        $user = auth()->user();

        if (! $user->groups()->where('groups.id', $group->id)->exists()) {
            return back()->with('success', 'You are not a member of this group.');
        }

        $user->groups()->detach($group->id);

        return back()->with('success', 'You left the group.');
    }
}
