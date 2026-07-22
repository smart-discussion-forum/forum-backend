<?php

namespace App\Http\Controllers;

use App\Events\NewPostCreated;
use App\Models\Topic;
use App\Models\Post;
use App\Models\Group;
use App\Notifications\NewTopicPosted;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TopicController extends Controller
{
    private function assertMember($groupId)
    {
        if (! auth()->user()->groups()->where('groups.id', $groupId)->exists()) {
            abort(403, 'You are not a member of this group.');
        }
    }

    public function groupIndex($groupId)
    {
        $this->assertMember($groupId);

        $group = Group::findOrFail($groupId);

        $topics = Topic::with('creator')
            ->where('group_id', $groupId)
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

        return view('topics.group-index', compact('topicSummaries', 'group'));
    }

    public function index(Request $request, $id = null)
    {
        $topics = Topic::with('user')->withCount('posts')->latest()->get();

        $selectedTopicId = $id ?? $request->query('topic');
        $topic = null;
        $posts = collect();

        if ($selectedTopicId) {
            $topic = Topic::with('user')->find($selectedTopicId);
            if ($topic) {
                $posts = $topic->posts()->with('user')->latest()->get();
            }
        }

        return view('discussions.index', compact('topics', 'topic', 'posts'));
    }

    public function groupCreate($groupId)
    {
        $this->assertMember($groupId);

        $group = Group::findOrFail($groupId);

        return view('topics.group-create', compact('group'));
    }

    public function groupStore(Request $request, $groupId)
    {
        $this->assertMember($groupId);

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:100',
        ]);

        $topic = Topic::create([
            'group_id' => $groupId,
            'created_by' => auth()->id(),
            'title' => $data['title'],
            'category' => $data['category'] ?? null,
        ]);

        foreach ($topic->group->members as $member) {
            if ($member->id !== auth()->id()) {
                $member->notify(new NewTopicPosted($topic));
            }
        }

        return redirect('/groups/' . $groupId . '/topics/' . $topic->id);
    }

    public function groupShow($groupId, $id)
    {
        $this->assertMember($groupId);

        $group = Group::findOrFail($groupId);
        $topic = Topic::with('creator')->where('group_id', $groupId)->findOrFail($id);
        $posts = $topic->posts()->with('user')->orderBy('created_at', 'asc')->get();
        $reactedPostIds = [];
        $topics = Topic::with('creator')->where('group_id', $groupId)->latest()->get();

        return view('discussions.group-show', compact('topic', 'topics', 'posts', 'reactedPostIds', 'group'));
    }

    public function groupStorePost(Request $request, $groupId, $topicId)
    {
        $this->assertMember($groupId);

        $data = $request->validate([
            'content' => 'required|string',
        ]);

        $topic = Topic::where('group_id', $groupId)->findOrFail($topicId);

        // Ignore accidental double-submits of the same reply within a few seconds.
        $existing = Post::where('topic_id', $topic->id)
            ->where('user_id', auth()->id())
            ->where('content', $data['content'])
            ->where('created_at', '>=', now()->subSeconds(5))
            ->latest('id')
            ->first();

        if ($existing) {
            $existing->load('user:id,name', 'topic:id,group_id');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'post' => $existing,
                ]);
            }

            return redirect('/groups/' . $groupId . '/topics/' . $topicId);
        }

        $post = Post::create([
            'user_id' => auth()->id(),
            'topic_id' => $topic->id,
            'content' => $data['content'],
        ]);

        $post->load('user:id,name', 'topic:id,group_id');

        try {
            broadcast(new NewPostCreated($post))->toOthers();
        } catch (\Throwable $e) {
            Log::warning('Realtime topic post broadcast failed: ' . $e->getMessage(), [
                'post_id' => $post->id,
                'topic_id' => $topic->id,
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'post' => $post,
            ], 201);
        }

        return redirect('/groups/' . $groupId . '/topics/' . $topicId);
    }
}