<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\Post;
use App\Models\PostReaction;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::with('creator')
            ->withCount('posts')
            ->latest()
            ->get();

        $topicSummaries = $topics->map(function ($topic) {
            return [
                'topic' => $topic,
                'latest_post' => $topic->posts()->with('user')->latest()->first(),
                'post_count' => $topic->posts_count,
            ];
        });

        return view('topics.index', compact('topicSummaries'));
    }

    public function discussions()
    {
        $topics = Topic::with('creator')->latest()->get();
        $activeTopic = $topics->first();
        $posts = $activeTopic ? $activeTopic->posts()->with('user')->withCount('reactions')->latest()->get() : collect();
        $reactedPostIds = $posts->isNotEmpty()
            ? PostReaction::where('user_id', auth()->id())->whereIn('post_id', $posts->pluck('id'))->pluck('post_id')->all()
            : [];

        return view('discussions.index', compact('topics', 'activeTopic', 'posts', 'reactedPostIds'));
    }

    public function create()
    {
        return view('topics.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        $topic = Topic::create([
            'user_id' => auth()->id(),
            'title' => $data['title'],
            'category' => $data['category'] ?? null,
        ]);

        return redirect('/topics/' . $topic->id);
    }

    public function show($id)
    {
        $topic = Topic::with('creator')->findOrFail($id);
        $posts = $topic->posts()->with('user')->withCount('reactions')->latest()->get();
        $reactedPostIds = $posts->isNotEmpty()
            ? PostReaction::where('user_id', auth()->id())->whereIn('post_id', $posts->pluck('id'))->pluck('post_id')->all()
            : [];
        $topics = Topic::with('creator')->latest()->get();

        return view('discussions.index', compact('topic', 'topics', 'posts', 'reactedPostIds'));
    }

    public function storePost(Request $request, $topicId)
    {
        $data = $request->validate([
            'content' => 'required|string',
        ]);

        Post::create([
            'user_id' => auth()->id(),
            'topic_id' => $topicId,
            'content' => $data['content'],
        ]);

        return redirect('/discussions/' . $topicId);
    }

    public function toggleReaction(Request $request, $topicId, $postId)
    {
        $post = Post::where('topic_id', $topicId)->findOrFail($postId);

        $existingReaction = PostReaction::where('user_id', auth()->id())
            ->where('post_id', $post->id)
            ->first();

        if ($existingReaction) {
            $existingReaction->delete();
        } else {
            PostReaction::create([
                'user_id' => auth()->id(),
                'post_id' => $post->id,
                'reaction_type' => 'like',
            ]);
        }

        return back();
    }
}
