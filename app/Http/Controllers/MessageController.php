<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function store(Request $request, $receiver_id)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'receiver_id' => $receiver_id,
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);

        // Broadcast the message
        broadcast(new MessageSent($message, $receiver_id, Auth::id()))->toOthers();

        return response()->json(['status' => 'Message sent']);
    }

    public function conversation($receiver_id)
    {
        $sender_id = Auth::id();

        $messages = Message::where(function ($query) use ($sender_id, $receiver_id) {
                $query->where('sender_id', $sender_id)
                      ->where('receiver_id', $receiver_id);
            })
            ->orWhere(function ($query) use ($sender_id, $receiver_id) {
                $query->where('sender_id', $receiver_id)
                      ->where('receiver_id', $sender_id);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }
}

