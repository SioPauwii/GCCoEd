<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Message;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // public int $receiver_id;
    public $message;
    public int $receiver_id;
    public int $sender_id;
    /**
     * Create a new event instance.
     */
    public function __construct(Message $message, int $receiver_id, int $sender_id)
    {
        $this->receiver_id = $receiver_id;
        $this->message = $message;
        $this->sender_id = $sender_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): PrivateChannel
    {
        return new PrivateChannel('chat.'.$this->receiver_id);
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message,
            'receiver_id' => $this->receiver_id,
            'sender_id' => $this->sender_id,
        ];
    }

    public function broadcastAs(): string
    {
        return 'MessageSent';
    }
}
