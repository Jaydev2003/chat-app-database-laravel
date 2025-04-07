<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
class UserStatusUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $userId;
    public $status;

    public function __construct($user)
{
    $this->userId = $user->id;
    $this->status = Cache::has('user-online-' . $user->id)
        ? 'Online'
        : Carbon::parse($user->last_seen)->diffForHumans();
}


    public function broadcastOn()
    {
        return new PrivateChannel('user-status.' . $this->userId);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->userId,
            'status' => $this->status,
        ];
    }
}    