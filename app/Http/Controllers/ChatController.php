<?php

namespace App\Http\Controllers;

class ChatController extends Controller
{
    public function index()
    {
        $groups = auth()->user()
            ->groups()
            ->with('members:id,name')
            ->orderBy('name')
            ->get();

        $groupsData = $groups->map(function ($group) {
            return [
                'id' => $group->id,
                'name' => $group->name,
                'members' => $group->members->map(function ($member) {
                    return [
                        'id' => $member->id,
                        'name' => $member->name,
                    ];
                })->values()->all(),
            ];
        })->values()->all();

        return view('chat.index', compact('groups', 'groupsData'));
    }
}
