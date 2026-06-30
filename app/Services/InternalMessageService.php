<?php

namespace App\Services;

use App\Models\Conversation;
use App\Models\InternalMessage;
use Illuminate\Support\Facades\DB;

class InternalMessageService
{
    public function sendMessage(int $senderId, array $participantIds, string $body, ?string $subject = null)
    {
        return DB::transaction(function() use ($senderId, $participantIds, $body, $subject) {
            // Find or create conversation
            $conversation = Conversation::create([
                'type' => 'internal',
                'subject' => $subject,
                'last_message_at' => now(),
            ]);

            // Add participants
            $allParticipants = array_unique(array_merge([$senderId], $participantIds));
            $conversation->users()->attach($allParticipants);

            // Create message
            $message = InternalMessage::create([
                'conversation_id' => $conversation->id,
                'sender_id' => $senderId,
                'body' => $body,
                'is_read' => false,
            ]);

            // TODO: Broadcast event via Reverb for realtime
            // broadcast(new MessageSent($message))->toOthers();

            return $message;
        });
    }
}
