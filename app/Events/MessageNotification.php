<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageNotification implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $receiverId;
    public $senderId;
    public $senderName;

    public function __construct($receiverId, $senderId, $senderName)
    {
        $this->receiverId = $receiverId;
        $this->senderId = $senderId;
        $this->senderName = $senderName;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->receiverId);
    }

    public function broadcastWith()
    {
        $unreadCount = Message::where('sender_id', $this->senderId)
            ->where('receiver_id', $this->receiverId)
            ->where('is_read', false)
            ->count();

        return [
            'notification' => "New message from {$this->senderName}",
            'sender_id' => $this->senderId,
            'unread_count' => $unreadCount,
        ];
    }
}
