<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use App\Models\Post;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function index()
    {
        $topics = Topic::with('user')->latest()->get();
        return view('topics.index', compact('topics'));
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
        $topic = Topic::with('user')->findOrFail($id);
        $posts = $topic->posts()->with('user')->latest()->get();
        return view('topics.show', compact('topic', 'posts'));
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

        return redirect('/topics/' . $topicId);
    }
}
