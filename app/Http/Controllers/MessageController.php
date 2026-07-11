<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'content' => 'required|string',
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
        $message->load('sender');

        broadcast(new MessageSent($message))->toOthers();

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
