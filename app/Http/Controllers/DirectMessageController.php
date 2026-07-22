<?php

namespace App\Http\Controllers;

use App\Models\DirectMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DirectMessageController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $user = Auth::user();

        $message = DirectMessage::create([
            'sender_id' => $user->id,
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function getConversation($userId)
    {
        $authId = Auth::id();

        $messages = DirectMessage::where(function ($q) use ($authId, $userId) {
            $q->where('sender_id', $authId)->where('receiver_id', $userId);
        })->orWhere(function ($q) use ($authId, $userId) {
            $q->where('sender_id', $userId)->where('receiver_id', $authId);
        })
        ->orderBy('created_at', 'asc')
        ->get();

        return response()->json($messages);
    }
}
