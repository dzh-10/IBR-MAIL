<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\ChatMessage;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConversationController extends Controller
{
    /**
     * List all conversations for the authenticated user.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $conversations = Conversation::with(['user', 'recipient'])
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                      ->orWhere('recipient_id', $userId);
            })
            ->orderBy('last_message_at', 'desc')
            ->paginate(15);

        return response()->json($conversations);
    }

    /**
     * Retrieve chat messages of a conversation.
     */
    public function messages(Request $request, $id)
    {
        $userId = Auth::id();
        $conversation = Conversation::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->orWhere('recipient_id', $userId);
        })->findOrFail($id);

        // Fetch messages paginated, oldest first or newest first?
        // Usually we fetch newest first for pagination, and let frontend order it.
        $messages = ChatMessage::where('conversation_id', $conversation->id)
            ->with('sender')
            ->orderBy('created_at', 'desc')
            ->paginate(30);

        // Mark messages as read if the recipient views them
        ChatMessage::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json($messages);
    }

    /**
     * Store a new chat message in an existing conversation.
     */
    public function storeMessage(Request $request, $id)
    {
        $userId = Auth::id();
        $conversation = Conversation::where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->orWhere('recipient_id', $userId);
        })->findOrFail($id);

        $request->validate([
            'body' => 'required|string',
        ]);

        $message = ChatMessage::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $userId,
            'body' => $request->body,
            'is_read' => false
        ]);

        $conversation->update([
            'last_message_at' => now(),
        ]);

        // Load sender relationship for broadcasting
        $message->load('sender');

        // Broadcast to WebSocket private channel
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message, 201);
    }

    /**
     * Create a new conversation or return existing one.
     */
    public function store(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'subject' => 'nullable|string',
        ]);

        $userId = Auth::id();
        $recipientId = $request->recipient_id;

        if ($userId == $recipientId) {
            return response()->json(['message' => 'You cannot chat with yourself.'], 400);
        }

        // Check if conversation already exists
        $conversation = Conversation::where(function ($query) use ($userId, $recipientId) {
            $query->where('user_id', $userId)->where('recipient_id', $recipientId);
        })->orWhere(function ($query) use ($userId, $recipientId) {
            $query->where('user_id', $recipientId)->where('recipient_id', $userId);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'type' => 'internal',
                'subject' => $request->subject,
                'user_id' => $userId,
                'recipient_id' => $recipientId,
                'last_message_at' => now(),
            ]);
        }

        return response()->json($conversation->load(['user', 'recipient']), 201);
    }
}
