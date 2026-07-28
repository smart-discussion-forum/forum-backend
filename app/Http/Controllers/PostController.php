<?php

namespace App\Http\Controllers;

use App\Events\NewPostCreated;
use App\Models\Post;
use App\Models\PostReaction;
use App\Models\Topic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // Public, unauthenticated preview page for external social sharing.
    // Deliberately does NOT check group membership — it's meant to be
    // safe to open by anyone with the link (social crawlers included),
    // and only exposes a truncated excerpt via Post::shareExcerpt.
    public function sharePreview($id)
    {
        $post = Post::with('user:id,name', 'topic:id,title')->findOrFail($id);

        return view('posts.share', compact('post'));
    }

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
        $user->touchLastActive();

        broadcast(new NewPostCreated($post))->toOthers();

        return response()->json([
            'success' => true,
            'post' => $post,
        ], 201);
    }

    // Toggle a "like" reaction on a post from the discussions thread view.
    public function react(Request $request, $topicId, $postId)
    {
        $topic = Topic::findOrFail($topicId);
        $user = Auth::user();

        if (! $user->groups()->where('groups.id', $topic->group_id)->exists()) {
            abort(403, 'You are not a member of this group.');
        }

        $post = Post::where('topic_id', $topicId)->findOrFail($postId);

        $existing = PostReaction::where('user_id', $user->id)
            ->where('post_id', $post->id)
            ->first();

        if ($existing) {
            $existing->delete();
        } else {
            PostReaction::create([
                'user_id' => $user->id,
                'post_id' => $post->id,
                'reaction_type' => 'like',
            ]);
        }

        return redirect('/discussions/' . $topicId);
    }
}