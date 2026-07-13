<?php

namespace App\Http\Controllers\Api;

use App\Events\NewPostCreated;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index($topicId)
    {
        $topic = Topic::findOrFail($topicId);
        $user = Auth::user();

        if (! $user->groups()->where('groups.id', $topic->group_id)->exists()) {
            return response()->json(['message' => 'You are not a member of this group.'], 403);
        }

        $posts = Post::where('topic_id', $topicId)
            ->with('user:id,name')
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($posts);
    }

    public function store(Request $request, $topicId)
    {
        $topic = Topic::findOrFail($topicId);
        $user = Auth::user();

        if (! $user->groups()->where('groups.id', $topic->group_id)->exists()) {
            return response()->json(['message' => 'You are not a member of this group.'], 403);
        }

        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $post = Post::create([
            'topic_id' => $topicId,
            'user_id' => $user->id,
            'content' => $data['content'],
        ]);

        $post->load('user:id,name', 'topic:id,group_id');

        broadcast(new NewPostCreated($post))->toOthers();

        return response()->json([
            'success' => true,
            'post' => $post,
        ], 201);
    }
}