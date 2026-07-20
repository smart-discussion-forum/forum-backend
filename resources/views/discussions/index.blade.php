@extends('layouts.app')
@section('content')
    @php
        $selectedTopic = $topic ?? $activeTopic ?? null;
        $currentUserId = auth()->id();
    @endphp

    <div class="page-card" style="max-width:1320px; margin:24px auto; padding:18px;">
        <div class="discussion-shell">
            <aside class="discussion-sidebar">
                <div class="discussion-sidebar-header">
                    <div>
                        <div class="screen-title" style="margin-bottom:6px; text-align:left; color:var(--text);">Discussions</div>
                        <div class="sidebar-copy">Open a topic thread to see the conversation.</div>
                    </div>
                    <a href="/topics/create" class="chat-btn">New topic</a>
                </div>

                <div class="topic-list">
                    @forelse($topics as $topicItem)
                        <a href="/discussions/{{ $topicItem->id }}" class="topic-card {{ $selectedTopic && $selectedTopic->id === $topicItem->id ? 'active' : '' }}">
                            <div class="topic-card-title">{{ $topicItem->title }}</div>
                            <div class="topic-card-meta">
                                {{ $topicItem->user?->name ?? 'Unknown author' }}
                                @if($topicItem->category)
                                    <span class="topic-dot">•</span>{{ $topicItem->category }}
                                @endif
                            </div>
                        </a>
                    @empty
                        <div class="empty-panel">No topics yet. Create the first discussion to get started.</div>
                    @endforelse
                </div>
            </aside>

            <section class="discussion-conversation">
                @if($selectedTopic)
                    <div class="conversation-header">
                        <div>
                            <div class="conversation-title">{{ $selectedTopic->title }}</div>
                            <div class="conversation-subtitle">
                                Started by {{ $selectedTopic->user?->name ?? 'Unknown author' }}
                                @if($selectedTopic->category)
                                    <span class="topic-dot">•</span>{{ $selectedTopic->category }}
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="chat-thread">
                        @forelse($posts as $post)
                            <div class="chat-row {{ $post->user_id === $currentUserId ? 'mine' : '' }}">
                                <div class="chat-bubble">
                                    <div class="chat-meta">{{ $post->user?->name ?? 'Unknown user' }}</div>
                                    <div class="chat-text">{{ $post->content }}</div>
                                    <div class="chat-time">
                                        {{ optional($post->created_at)->format('M j, Y g:i A') ?? '' }}
                                    </div>
                                    <div class="chat-actions">
                                        <form method="POST" action="/topics/{{ $selectedTopic->id }}/posts/{{ $post->id }}/reaction">
                                            @csrf
                                            <button type="submit" class="reaction-btn {{ in_array($post->id, $reactedPostIds ?? []) ? 'active' : '' }}">
                                                {{ in_array($post->id, $reactedPostIds ?? []) ? 'Unlike' : 'Like' }}
                                                @if(isset($post->reactions_count))
                                                    ({{ $post->reactions_count }})
                                                @endif
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-panel">No discussion yet. Be the first to reply.</div>
                        @endforelse
                    </div>

                    <form method="POST" action="/topics/{{ $selectedTopic->id }}/posts" class="reply-form">
                        @csrf
                        <textarea name="content" placeholder="Type your reply..."></textarea>
                        <div class="reply-actions">
                            <button type="submit" class="chat-btn">Send</button>
                        </div>
                    </form>
                @else
                    <div class="empty-conversation">
                        <div class="conversation-title">Select a topic</div>
                        <p>Pick a discussion from the left to open the chat thread, or start a new topic.</p>
                    </div>
                @endif
            </section>
        </div>
    </div>
@endsection