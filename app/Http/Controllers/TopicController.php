<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\Post;
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
        $posts = $activeTopic ? $activeTopic->posts()->with('user')->latest()->get() : collect();
        $reactedPostIds = []; // reactions not available yet (no post_reactions table)

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
            'group_id' => 'required|exists:groups,id',
        ]);

        $topic = Topic::create([
            'group_id' => $data['group_id'],
            'created_by' => auth()->id(),
            'title' => $data['title'],
            'category' => $data['category'] ?? null,
        ]);

        return redirect('/topics/' . $topic->id);
    }

    public function show($id)
    {
        $topic = Topic::with('creator')->findOrFail($id);
        $posts = $topic->posts()->with('user')->latest()->get();
        $reactedPostIds = []; // reactions not available yet (no post_reactions table)
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
}