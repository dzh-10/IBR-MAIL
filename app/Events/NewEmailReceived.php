<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewEmailReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, Queueable, SerializesModels;

    public $messageModel;

    /**
     * Create a new event instance.
     */
    public function __construct(Message $messageModel)
    {
        $this->messageModel = $messageModel;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // Broadcast to the user who owns this mail account
        return [
            new PrivateChannel('user.' . $this->messageModel->mailAccount->user_id),
        ];
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'id' => $this->messageModel->id,
            'subject' => $this->messageModel->subject,
            'from_email' => $this->messageModel->from_email,
            'from_name' => $this->messageModel->from_name,
            'received_at' => $this->messageModel->received_at ? $this->messageModel->received_at->toIso8601String() : now()->toIso8601String(),
        ];
    }
}
