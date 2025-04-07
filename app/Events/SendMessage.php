<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class SendMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;
    public $isGroup;
    public $groupId;

    public function __construct(Message $message, bool $isGroup, $groupId = null)
    {
        $this->message = $message->load('sender'); 
        $this->isGroup = $isGroup;
        $this->groupId = $groupId;
    }

    public function broadcastOn()
{
    if ($this->isGroup && $this->groupId) {
        return new PrivateChannel('group.chat.' . $this->groupId);
    } else {
        return [
            new PrivateChannel('chat.' . $this->message->sender_id),
            new PrivateChannel('chat.' . $this->message->receiver_id),
        ];
    }
}


public function broadcastWith()
{
    $unreadCount = 0;

    if (!$this->isGroup) {
        $unreadCount = Message::where('receiver_id', $this->message->receiver_id)
            ->where('sender_id', $this->message->sender_id)
            ->where('is_read', false)
            ->count();

        Log::info("Unread messages from sender {$this->message->sender_id} to receiver {$this->message->receiver_id}: {$unreadCount}");
    }

    return [
        'sender_id' => $this->message->sender_id,
        'sender_name' => $this->message->sender->name ?? 'Unknown',
        'receiver_id' => $this->isGroup ? null : $this->message->receiver_id,
        'group_id' => $this->isGroup ? $this->groupId : null,
        'message' => Crypt::decryptString($this->message->message),
        'file_url' => $this->message->file_path ? asset("http://127.0.0.1:8000/storage/{$this->message->file_path}") : null,
        'file_type' => $this->message->file_type ?? null,
        'time' => $this->message->created_at->format('H:i'),
        'unread_count' => $unreadCount,
        'notification' => "You have a new message from " . $this->message->sender->name,
    ];
}



}

