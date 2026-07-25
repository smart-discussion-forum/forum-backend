<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\MessageExclusion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'content' => 'required|string',
            'excluded_user_ids' => 'sometimes|array',
            'excluded_user_ids.*' => 'integer|exists:users,id',
        ]);

        $user = Auth::user();

        if (! $user->groups()->where('groups.id', $request->group_id)->exists()) {
            return response()->json(['message' => 'You are not a member of this group.'], 403);
        }

        $message = Message::create([
            'group_id' => $request->group_id,
            'sender_id' => $user->id,
            'content' => $request->content,
            'sent_at' => now(),
        ]);

        $user->touchLastActive();

        $excludedIds = collect($request->input('excluded_user_ids', []))
            ->filter(fn ($id) => (int) $id !== $user->id)
            ->unique()
            ->values();

        foreach ($excludedIds as $excludedUserId) {
            MessageExclusion::create([
                'message_id' => $message->id,
                'excluded_user_id' => $excludedUserId,
            ]);
        }

        $message->load('sender');

        try {
            broadcast(new MessageSent($message))->toOthers();
        } catch (\Throwable $e) {
            Log::warning('Realtime chat broadcast failed: ' . $e->getMessage(), [
                'message_id' => $message->id,
                'group_id' => $request->group_id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => $message->load('sender'),
        ]);
    }

    public function getMessages($groupId)
    {
        $user = Auth::user();

        if (! $user->groups()->where('groups.id', $groupId)->exists()) {
            return response()->json(['message' => 'You are not a member of this group.'], 403);
        }

        $messages = Message::where('group_id', $groupId)
            ->whereDoesntHave('exclusions', fn ($query) => $query->where('excluded_user_id', $user->id))
            ->with('sender:id,name')
            ->orderBy('sent_at', 'asc')
            ->get();

        return response()->json($messages);
    }
}
