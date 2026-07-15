<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TopicController extends Controller
{
    public function index($groupId)
    {
        $user = Auth::user();

        if (! $user->groups()->where('groups.id', $groupId)->exists()) {
            return response()->json(['message' => 'You are not a member of this group.'], 403);
        }

        $topics = Topic::where('group_id', $groupId)
            ->with('creator:id,name')
            ->withCount('posts')
            ->latest()
            ->get();

        return response()->json($topics);
    }

    public function store(Request $request, $groupId)
    {
        $user = Auth::user();

        if (! $user->groups()->where('groups.id', $groupId)->exists()) {
            return response()->json(['message' => 'You are not a member of this group.'], 403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        $topic = Topic::create([
            'group_id' => $groupId,
            'created_by' => $user->id,
            'title' => $data['title'],
            'category' => $data['category'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'topic' => $topic->load('creator:id,name'),
        ], 201);
    }

    public function show($topicId)
    {
        $topic = Topic::with('creator:id,name')->findOrFail($topicId);

        $user = Auth::user();
        if (! $user->groups()->where('groups.id', $topic->group_id)->exists()) {
            return response()->json(['message' => 'You are not a member of this group.'], 403);
        }

        $posts = $topic->posts()->with('user:id,name')->orderBy('created_at', 'asc')->get();

        return response()->json([
            'topic' => $topic,
            'posts' => $posts,
        ]);
    }

    public function search(Request $request)
    {
        $user = Auth::user();

        $query = Topic::query()->with('creator:id,name')->withCount('posts');
        $query->whereIn('group_id', $user->groups()->pluck('groups.id'));

        if ($request->filled('group_id')) {
            $query->where('group_id', $request->group_id);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('keyword')) {
            $query->where('title', 'like', '%'.$request->keyword.'%');
        }

        return response()->json($query->latest()->get());
    }
}