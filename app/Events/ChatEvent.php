<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $message;
    // public $sender;
    // public $receiver;
    // public $conversation;
    // public $type;
    // public $isTyping;
    // public $isRead;
    // public $isDelivered;
    // public $isSeen;
    // public $isDeliveredAt;
    // public $isSeenAt;
    // public $isReadAt;
    public function __construct(Message $message)
    {
        //
        $this->message = $message;
        // $this->sender = new Sender();
        // $this->receiver = new Receiver();
        // $this->conversation = new Conversation();
        // $this->type = new ChatType();
        // $this->isTyping = new IsTyping();
        // $this->isRead = new IsRead();
        // $this->isDelivered = new IsDelivered();
        // $this->isSeen = new IsSeen();
        // $this->isDeliveredAt = new IsDeliveredAt();
        // $this->isSeenAt = new IsSeenAt();
        // $this->isReadAt = new IsReadAt();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return ['public'];
    }
    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastAs(): string
    {
        return 'chat';
    }
}
